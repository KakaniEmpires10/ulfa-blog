<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\BlogPostModel;
use App\Models\CategoryModel;
use App\Models\TagModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminPostController extends BaseAdminController
{
    protected $blogPostModel, $categoryModel, $tagModel;

    public function __construct()
    {
        helper('web');

        $this->blogPostModel = new BlogPostModel();
        $this->categoryModel = new CategoryModel();
        $this->tagModel = new TagModel();
    }

    public function index()
    {
        return $this->renderAdmin('pages/admin/blog-posts/index', [
            'title'            => 'Manajemen Blog',
            'pageTitle'        => 'Manajemen Blog',
            'pageDescription'  => 'Halaman tag sudah tersedia. Pada tahap berikutnya kita bisa tambahkan list tag, pencarian, serta aksi tambah dan ubah.',
            'hasAction'        => true,
            'actionUrl'        => site_url('/admin/posts/create'),
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Manajemen Blog', 'url' => null],
            ],
            'postDataUrl'     => site_url('/admin/posts-data'),
        ]);
    }

    public function data(): ResponseInterface
    {
        $filters = [
            'title'     => $this->request->getGet('title'),
            'status'    => $this->request->getGet('status'),
            'startDate' => $this->request->getGet('startDate'),
            'endDate'   => $this->request->getGet('endDate'),
        ];

        $result = $this->blogPostModel->getAdminPosts($filters, 15);

        return $this->response->setJSON([
            'data'        => $result['posts'],
            'total_pages' => $result['pager']->getPageCount(),
            'current_page' => $result['pager']->getCurrentPage(),
            'total_items' => $result['pager']->getTotal()
        ]);
    }

    public function create()
    {
        return $this->renderAdmin('pages/admin/blog-posts/form', [
            'title'            => 'Buat Postingan Baru',
            'pageTitle'        => 'Buat Postingan Baru',
            'pageDescription'  => 'Halaman untuk membuat postingan baru.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Manajemen Blog', 'url' => site_url('/admin/posts')],
                ['title' => 'Buat Postingan Baru', 'url' => null],
            ],
            'categories'       => $this->categoryModel->findAll(),
            'tags'             => $this->tagModel->findAll(),
        ]);
    }

    public function store()
    {
        $db = \Config\Database::connect();

        $errors = $this->validatePostRequest(true);
        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $tagIds = $this->normalizeTagIds($this->request->getPost('tag_ids'));

        $db->transStart();
        try {
            $file = $this->request->getFile('cover_image');
            $media = $this->storeBlogMediaFile($file, $db, 'Gagal mengunggah file cover.');
            $fullPath = $media['file_path'];

            $postData = $this->buildPostPayload($fullPath);
            $postData['author_id'] = auth()->id();

            $postId = $this->blogPostModel->insert($postData);
            if (!$postId) {
                throw new \Exception('Gagal menyimpan data post.');
            }

            $db->table('blog_post_media')->insert(['post_id' => $postId, 'media_id' => $media['id'], 'type' => 'cover']);
            $db->table('blog_post_categories')->insert(['post_id' => $postId, 'category_id' => $this->request->getPost('category_id')]);

            if (!empty($tagIds)) {
                $tagsData = array_map(fn($tagId) => ['post_id' => $postId, 'tag_id' => $tagId], $tagIds);
                $db->table('blog_post_tags')->insertBatch($tagsData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal.');
            }

            return redirect()->to(site_url('admin/posts'))->with('success', 'Blog post berhasil diterbitkan!');
        } catch (\Exception $e) {
            $db->transRollback();
            if (isset($fullPath) && file_exists(FCPATH . $fullPath)) unlink(FCPATH . $fullPath);
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $post = $this->blogPostModel->getEditPost($id);

        if (!$post) {
            return redirect()->to(site_url('/admin/posts'))->with('error', 'Postingan tidak ditemukan.');
        }

        return $this->renderAdmin('pages/admin/blog-posts/form', [
            'title'            => 'Edit Postingan',
            'pageTitle'        => 'Edit Postingan',
            'pageDescription'  => 'Halaman untuk mengedit postingan.',
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Manajemen Blog', 'url' => site_url('/admin/posts')],
                ['title' => 'Edit Postingan', 'url' => null],
            ],
            'post'             => $post,
            'categories'       => $this->categoryModel->findAll(),
            'tags'             => $this->tagModel->findAll()
        ]);
    }

    public function update($id)
    {
        $db = \Config\Database::connect();
        $existingPost = $this->blogPostModel->find($id);
        if (!$existingPost) return redirect()->back()->with('error', 'Data tidak ditemukan.');

        $file = $this->request->getFile('cover_image');
        $isNewFileUploaded = $this->hasSubmittedCoverImage($file);

        $errors = $this->validatePostRequest(false);
        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $tagIds = $this->normalizeTagIds($this->request->getPost('tag_ids'));

        $db->transStart();
        try {
            $postData = $this->buildPostPayload();

            $tempNewFilePath = null;

            if ($isNewFileUploaded) {
                $newMedia = $this->storeBlogMediaFile($file, $db, 'Gagal memindahkan file baru ke server.');
                $tempNewFilePath = $newMedia['file_path'];

                $oldMedia = $db->table('blog_post_media')->where(['post_id' => $id, 'type' => 'cover'])->get()->getRow();

                $db->table('blog_post_media')->where(['post_id' => $id, 'type' => 'cover'])->delete();
                $db->table('blog_post_media')->insert([
                    'post_id'  => $id,
                    'media_id' => $newMedia['id'],
                    'type'     => 'cover'
                ]);

                $postData['cover_image'] = $tempNewFilePath;
            }

            $this->blogPostModel->update($id, $postData);

            $db->table('blog_post_categories')->where('post_id', $id)->delete();
            $db->table('blog_post_categories')->insert(['post_id' => $id, 'category_id' => $this->request->getPost('category_id')]);

            $db->table('blog_post_tags')->where('post_id', $id)->delete();
            if (!empty($tagIds) && is_array($tagIds)) {
                $tagsData = array_map(fn($tagId) => ['post_id' => $id, 'tag_id' => $tagId], $tagIds);
                $db->table('blog_post_tags')->insertBatch($tagsData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal.');
            }

            if ($isNewFileUploaded && isset($oldMedia)) {
                $db->table('media')->where('id', $oldMedia->media_id)->delete();
                if (!empty($existingPost['cover_image']) && file_exists(FCPATH . $existingPost['cover_image'])) {
                    unlink(FCPATH . $existingPost['cover_image']);
                }
            }

            return redirect()->to(site_url('admin/posts'))->with('success', 'Berita berhasil diperbarui!');
        } catch (\Exception $e) {
            $db->transRollback();

            if (isset($tempNewFilePath) && file_exists(FCPATH . $tempNewFilePath)) {
                unlink(FCPATH . $tempNewFilePath);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $json = $this->request->getJSON();
        $status = $json->status ?? null;

        if (!in_array($status, ['draft', 'published'], true)) {
            return $this->response->setStatusCode(422)->setJSON(['message' => 'Status postingan tidak valid.']);
        }

        if ($this->blogPostModel->update($id, ['status' => $status])) {
            return $this->response->setJSON(['status' => 'success']);
        }

        return $this->response->setStatusCode(500)->setJSON(['message' => 'Gagal update']);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();

        try {
            $post = $this->blogPostModel->find($id);
            if (!$post) {
                return redirect()->back()->with('error', 'Data tidak ditemukan.');
            }

            $postAssetPaths = $this->collectPostAssetPaths($post);

            $db->transException(true);
            $db->transStart();

            $db->table('blog_post_categories')->where('post_id', $id)->delete();
            $db->table('blog_post_tags')->where('post_id', $id)->delete();
            $db->table('blog_post_media')->where('post_id', $id)->delete();

            $this->blogPostModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Gagal menghapus postingan.');
            }

            $this->cleanupUnusedPostAssets($id, $postAssetPaths);

            return redirect()->to(site_url('admin/posts'))->with('success', 'Postingan berhasil dihapus.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to(site_url('admin/posts'))->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function uploadImage()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        $file = $this->request->getFile('upload');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'error' => [
                    'message' => 'File tidak valid atau tidak ditemukan'
                ],
                'csrfHash' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON([
                'error' => [
                    'message' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WEBP.'
                ],
                'csrfHash' => csrf_hash(),
            ])->setStatusCode(400);
        }

        if ($file->getSize() > 3 * 1024 * 1024) {
            return $this->response->setJSON([
                'error' => [
                    'message' => 'Ukuran file maksimal 3MB.'
                ],
                'csrfHash' => csrf_hash(),
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $fullPath = null;

        try {
            $media = $this->storeBlogMediaFile($file, $db, 'Gagal mengupload gambar. Silakan coba lagi.');
            $fullPath = $media['file_path'];

            return $this->response->setJSON([
                'url'      => base_url($fullPath),
                'csrfHash' => csrf_hash(),
            ]);
        } catch (\Throwable $exception) {
            $storedFile = FCPATH . $fullPath;

            if (is_file($storedFile)) {
                unlink($storedFile);
            }

            return $this->response->setJSON([
                'error' => [
                    'message' => $exception->getMessage(),
                ],
                'csrfHash' => csrf_hash(),
            ])->setStatusCode(500);
        }
    }

    private function validatePostRequest(bool $requireCoverImage): array
    {
        $rules = [
            'title'        => 'required|min_length[5]|max_length[255]',
            'excerpt'      => 'permit_empty|max_length[500]',
            'content'      => 'required',
            'category_id'  => 'required|is_not_unique[blog_categories.id]',
            'published_at' => 'permit_empty|valid_date[Y-m-d H:i:s]',
            'status'       => 'permit_empty|in_list[published]',
        ];

        $file = $this->request->getFile('cover_image');
        if ($requireCoverImage || $this->hasSubmittedCoverImage($file)) {
            $rules['cover_image'] = ($requireCoverImage ? 'uploaded[cover_image]|' : '')
                . 'max_size[cover_image,3072]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png]';
        }

        $errors = [];
        if (!$this->validate($rules, $this->postValidationMessages())) {
            $errors = $this->validator->getErrors();
        }

        if (!isset($errors['content']) && plain_text_from_html($this->request->getPost('content')) === '') {
            $errors['content'] = 'Isi berita belum berisi teks yang bisa dibaca.';
        }

        $tagError = $this->validateTagIds($this->request->getPost('tag_ids'));
        if ($tagError !== null) {
            $errors['tag_ids'] = $tagError;
        }

        return $errors;
    }

    private function postValidationMessages(): array
    {
        return [
            'cover_image' => [
                'uploaded' => 'Cover image wajib diunggah. Pilih gambar sampul untuk berita Anda.',
                'max_size' => 'Ukuran gambar maksimal 3 MB. Silakan kompres gambar terlebih dahulu.',
                'is_image' => 'File yang dipilih bukan gambar. Harap unggah file gambar.',
                'mime_in'  => 'Format gambar tidak didukung. Gunakan JPG, JPEG, atau PNG saja.',
            ],
            'title' => [
                'required'   => 'Judul berita jangan dikosongkan ya, pembaca butuh judul untuk mulai membaca.',
                'min_length' => 'Judulnya terlalu pendek, coba buat minimal 5 karakter agar lebih jelas.',
                'max_length' => 'Wah, judulnya kepanjangan. Coba persingkat agar pas saat tampil di layar.',
            ],
            'excerpt' => [
                'max_length' => 'Ringkasan terlalu panjang. Batasi maksimal 500 karakter.',
            ],
            'content' => [
                'required' => 'Isi beritanya masih kosong, yuk ceritakan sesuatu yang menarik!',
            ],
            'category_id' => [
                'required'      => 'Silakan pilih kategori berita.',
                'is_not_unique' => 'Kategori yang dipilih tidak terdaftar di sistem.',
            ],
            'published_at' => [
                'valid_date' => 'Tanggal publikasi tidak valid.',
            ],
            'status' => [
                'in_list' => 'Status postingan tidak valid.',
            ],
        ];
    }

    private function validateTagIds($tagIds): ?string
    {
        if (empty($tagIds)) {
            return null;
        }

        if (!is_array($tagIds)) {
            return 'Format tag tidak valid.';
        }

        foreach ($tagIds as $tagId) {
            if (!is_numeric($tagId) || (int) $tagId <= 0) {
                return 'Tag yang dipilih tidak valid.';
            }
        }

        $normalizedTagIds = $this->normalizeTagIds($tagIds);
        if ($normalizedTagIds === []) {
            return null;
        }

        $existingTags = \Config\Database::connect()
            ->table('blog_tags')
            ->whereIn('id', $normalizedTagIds)
            ->countAllResults();

        return $existingTags === count($normalizedTagIds)
            ? null
            : 'Salah satu tag yang dipilih tidak terdaftar.';
    }

    private function normalizeTagIds($tagIds): array
    {
        if (empty($tagIds) || !is_array($tagIds)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $tagIds)));
    }

    private function hasSubmittedCoverImage($file): bool
    {
        return $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
    }

    private function buildPostPayload(?string $coverImage = null): array
    {
        $title = trim((string) $this->request->getPost('title'));
        $excerpt = trim((string) $this->request->getPost('excerpt'));
        $content = (string) $this->request->getPost('content');

        $postData = [
            'title'           => $title,
            'content'         => $content,
            'excerpt'         => $excerpt,
            'status'          => $this->request->getPost('status') === 'published' ? 'published' : 'draft',
            'published_at'    => $this->request->getPost('published_at') ?: null,
            'seo_title'       => excerpt_text($title, 255),
            'seo_description' => blog_seo_description($excerpt, $content),
        ];

        if ($coverImage !== null) {
            $postData['cover_image'] = $coverImage;
        }

        return $postData;
    }

    private function storeBlogMediaFile($file, $db, string $moveErrorMessage): array
    {
        $uploadPath = 'uploads/blog/';
        $absoluteUploadPath = FCPATH . $uploadPath;

        if (!is_dir($absoluteUploadPath) && !mkdir($absoluteUploadPath, 0775, true) && !is_dir($absoluteUploadPath)) {
            throw new \RuntimeException('Direktori upload tidak bisa dibuat.');
        }

        $fileName = $file->getRandomName();
        $fullPath = $uploadPath . $fileName;

        if (!$file->move($absoluteUploadPath, $fileName)) {
            throw new \RuntimeException($moveErrorMessage);
        }

        try {
            $isStored = $db->table('media')->insert([
                'file_name'   => $fileName,
                'file_path'   => $fullPath,
                'mime_type'   => $file->getClientMimeType(),
                'size'        => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);

            if (!$isStored) {
                throw new \RuntimeException('Gagal menyimpan metadata gambar.');
            }

            return [
                'id'        => $db->insertID(),
                'file_name' => $fileName,
                'file_path' => $fullPath,
            ];
        } catch (\Throwable $exception) {
            $storedFile = FCPATH . $fullPath;
            if (is_file($storedFile)) {
                unlink($storedFile);
            }

            throw $exception;
        }
    }

    private function collectPostAssetPaths(array $post): array
    {
        $paths = [];

        $coverImage = $this->normalizeUploadedBlogPath($post['cover_image'] ?? '');
        if ($coverImage !== null) {
            $paths[] = $coverImage;
        }

        foreach ($this->extractBlogImagePathsFromContent((string) ($post['content'] ?? '')) as $path) {
            $paths[] = $path;
        }

        return array_values(array_unique(array_filter($paths)));
    }

    private function extractBlogImagePathsFromContent(string $content): array
    {
        if ($content === '') {
            return [];
        }

        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/i', $content, $matches);

        $paths = [];
        foreach ($matches[1] ?? [] as $src) {
            $path = $this->normalizeUploadedBlogPath($src);
            if ($path !== null) {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }

    private function normalizeUploadedBlogPath(string $path): ?string
    {
        $path = trim(html_entity_decode($path));
        if ($path === '') {
            return null;
        }

        if (preg_match('#(?:^|/)(uploads/blog/[^?"\'\s>]+)#i', $path, $matches)) {
            return str_replace('\\', '/', $matches[1]);
        }

        $parsedPath = parse_url($path, PHP_URL_PATH);
        if (is_string($parsedPath) && preg_match('#(?:^|/)(uploads/blog/[^?"\'\s>]+)#i', $parsedPath, $matches)) {
            return str_replace('\\', '/', $matches[1]);
        }

        return null;
    }

    private function cleanupUnusedPostAssets(int $deletedPostId, array $paths): void
    {
        if ($paths === []) {
            return;
        }

        $db = \Config\Database::connect();

        foreach ($paths as $path) {
            if ($this->isPostAssetStillReferenced($deletedPostId, $path)) {
                continue;
            }

            $mediaRows = $db->table('media')->where('file_path', $path)->get()->getResultArray();
            if ($mediaRows !== []) {
                $mediaIds = array_column($mediaRows, 'id');
                $db->table('blog_post_media')->whereIn('media_id', $mediaIds)->delete();
                $db->table('media')->whereIn('id', $mediaIds)->delete();
            }

            $absolutePath = FCPATH . $path;
            if (is_file($absolutePath)) {
                unlink($absolutePath);
            }
        }
    }

    private function isPostAssetStillReferenced(int $deletedPostId, string $path): bool
    {
        $db = \Config\Database::connect();

        $isUsedAsCover = $db->table('blog_posts')
            ->where('id !=', $deletedPostId)
            ->where('cover_image', $path)
            ->countAllResults() > 0;

        if ($isUsedAsCover) {
            return true;
        }

        return $db->table('blog_posts')
            ->where('id !=', $deletedPostId)
            ->like('content', $path)
            ->countAllResults() > 0;
    }
}
