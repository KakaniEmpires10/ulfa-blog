<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseAdminController;
use App\Models\BlogPostModel;
use App\Models\CategoryModel;
use App\Models\TagModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\HTTP\ResponseInterface;

class AdminPostController extends BaseAdminController
{
    protected $blogPostModel, $categoryModel, $tagModel;

    public function __construct()
    {
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
            'tags'             => $this->tagModel->findAll()
        ]);
    }

    public function store()
    {
        $db = \Config\Database::connect();

        $fileRules = [
            'cover_image' => 'uploaded[cover_image]|max_size[cover_image,3072]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png]'
        ];
        $fileMessages = [
            'cover_image' => [
                'uploaded' => 'Cover image wajib diunggah. Pilih gambar sampul untuk berita Anda.',
                'max_size' => 'Ukuran gambar maksimal 3 MB. Silakan kompres gambar terlebih dahulu.',
                'is_image' => 'File yang dipilih bukan gambar. Harap unggah file gambar.',
                'mime_in'  => 'Format gambar tidak didukung. Gunakan JPG, JPEG, atau PNG saja.'
            ]
        ];

        if (!$this->validate($fileRules, $fileMessages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $postData = [
            'title'        => $this->request->getPost('title'),
            'content'      => $this->request->getPost('content'),
            'excerpt'      => $this->request->getPost('excerpt'),
            'status'       => $this->request->getPost('status') ?? 'draft',
            'published_at' => $this->request->getPost('published_at'),
            'author_id'    => auth()->id(),
        ];

        if (!$this->blogPostModel->validate($postData)) {
            return redirect()->back()->withInput()->with('errors', $this->blogPostModel->errors());
        }

        $categoryId = $this->request->getPost('category_id');
        if (empty($categoryId) || !is_numeric($categoryId) || $categoryId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Pilih kategori yang valid.');
        }

        $categoryExists = $db->table('blog_categories')->where('id', $categoryId)->countAllResults();
        if ($categoryExists == 0) {
            return redirect()->back()->withInput()->with('error', 'Kategori yang dipilih tidak ditemukan.');
        }

        $tagIds = $this->request->getPost('tag_ids');
        if (!empty($tagIds)) {
            if (!is_array($tagIds)) {
                return redirect()->back()->withInput()->with('error', 'Format tag tidak valid.');
            }

            foreach ($tagIds as $tagId) {
                if (!is_numeric($tagId) || $tagId <= 0) {
                    return redirect()->back()->withInput()->with('error', 'ID tag harus berupa angka positif.');
                }
            }

            $existingTags = $db->table('blog_tags')->whereIn('id', $tagIds)->countAllResults();
            if ($existingTags != count($tagIds)) {
                return redirect()->back()->withInput()->with('error', 'Salah satu tag yang dipilih tidak terdaftar.');
            }
        }

        $db->transStart();

        try {
            $file = $this->request->getFile('cover_image');
            $fileName = $file->getRandomName();
            $uploadPath = 'uploads/blog/';

            if (!$file->move(FCPATH . $uploadPath, $fileName)) {
                throw new \Exception('Gagal mengunggah file cover. Periksa izin folder.');
            }

            $fullPath = $uploadPath . $fileName;

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
            $mediaId = $db->insertID();

            $postData['cover_image'] = $fullPath;
            $postId = $this->blogPostModel->insert($postData);

            if (!$postId) {
                throw new \Exception(implode(', ', $this->blogPostModel->errors()));
            }

            $db->table('blog_post_media')->insert([
                'post_id'  => $postId,
                'media_id' => $mediaId,
                'type'     => 'cover'
            ]);

            // 4. Insert category relation
            $db->table('blog_post_categories')->insert([
                'post_id'     => $postId,
                'category_id' => $categoryId
            ]);

            // 5. Insert tags
            if (!empty($tagIds)) {
                $tagsData = [];
                foreach ($tagIds as $tagId) {
                    $tagsData[] = [
                        'post_id' => $postId,
                        'tag_id'  => $tagId
                    ];
                }
                $db->table('blog_post_tags')->insertBatch($tagsData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat menyimpan data ke database.');
            }

            return redirect()->to(site_url('admin/posts'))->with('success', 'Berita berhasil diterbitkan!');
        } catch (DatabaseException $e) {
            $db->transRollback();
            // Hapus file yang sudah terupload jika terjadi error
            if (isset($fullPath) && file_exists(FCPATH . $fullPath)) {
                unlink(FCPATH . $fullPath);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $post = $this->blogPostModel->find($id);

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

        // Cek apakah post dengan ID ini ada
        $existingPost = $this->blogPostModel->find($id);
        if (!$existingPost) {
            return redirect()->back()->with('error', 'Data berita tidak ditemukan.');
        }

        // Siapkan validasi file (opsional)
        $uploadNewImage = false;
        $file = $this->request->getFile('cover_image');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadNewImage = true;
            $fileRules = [
                'cover_image' => 'max_size[cover_image,3072]|is_image[cover_image]|mime_in[cover_image,image/jpg,image/jpeg,image/png]'
            ];
            $fileMessages = [
                'cover_image' => [
                    'max_size' => 'Ukuran gambar maksimal 3 MB. Silakan kompres gambar Anda terlebih dahulu.',
                    'is_image' => 'File yang dipilih bukan gambar. Harap unggah file gambar (JPG, JPEG, PNG).',
                    'mime_in'  => 'Format gambar tidak didukung. Gunakan JPG, JPEG, atau PNG saja.'
                ]
            ];

            if (!$this->validate($fileRules, $fileMessages)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        // Validasi data post
        $postData = [
            'title'       => $this->request->getPost('title'),
            'content'     => $this->request->getPost('content'),
            'excerpt'     => $this->request->getPost('excerpt'),
            'status'      => $this->request->getPost('status') ?? 'draft',
            'published_at' => $this->request->getPost('published_at'),
        ];

        if (!$this->blogPostModel->validate($postData)) {
            return redirect()->back()->withInput()->with('errors', $this->blogPostModel->errors());
        }

        $categoryId = $this->request->getPost('category_id');
        if (empty($categoryId) || !is_numeric($categoryId) || $categoryId <= 0) {
            return redirect()->back()->withInput()->with('error', 'Pilih kategori yang valid.');
        }

        $categoryExists = $db->table('blog_categories')->where('id', $categoryId)->countAllResults();
        if ($categoryExists == 0) {
            return redirect()->back()->withInput()->with('error', 'Kategori yang dipilih tidak ditemukan.');
        }

        $tagIds = $this->request->getPost('tag_ids');
        if (!empty($tagIds)) {
            if (!is_array($tagIds)) {
                return redirect()->back()->withInput()->with('error', 'Format tag tidak valid.');
            }

            foreach ($tagIds as $tagId) {
                if (!is_numeric($tagId) || $tagId <= 0) {
                    return redirect()->back()->withInput()->with('error', 'ID tag harus berupa angka positif.');
                }
            }

            $existingTags = $db->table('blog_tags')->whereIn('id', $tagIds)->countAllResults();
            if ($existingTags != count($tagIds)) {
                return redirect()->back()->withInput()->with('error', 'Salah satu tag yang dipilih tidak terdaftar.');
            }
        }

        // Mulai transaksi
        $db->transStart();

        try {
            $uploadPath = 'uploads/blog/';
            $oldCoverPath = $existingPost['cover_image'];
            $oldMediaId = null;

            // Cari media_id dari cover lama
            $oldMedia = $db->table('blog_post_media')
                ->select('media_id')
                ->where('post_id', $id)
                ->where('type', 'cover')
                ->get()
                ->getRowArray();

            if ($oldMedia) {
                $oldMediaId = $oldMedia['media_id'];
            }

            // Jika upload gambar baru
            if ($uploadNewImage) {
                // Upload file baru
                $newFileName = $file->getRandomName();
                if (!$file->move(FCPATH . $uploadPath, $newFileName)) {
                    throw new \Exception('Gagal mengunggah gambar cover baru.');
                }

                $newFullPath = $uploadPath . $newFileName;

                // Insert ke tabel media (cover baru)
                $db->table('media')->insert([
                    'file_name'   => $newFileName,
                    'file_path'   => $newFullPath,
                    'mime_type'   => $file->getClientMimeType(),
                    'size'        => $file->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
                $newMediaId = $db->insertID();

                // Update relasi blog_post_media
                if ($oldMediaId) {
                    // Hapus relasi lama
                    $db->table('blog_post_media')
                        ->where('post_id', $id)
                        ->where('type', 'cover')
                        ->delete();

                    // Hapus record media lama dari tabel media
                    $db->table('media')->where('id', $oldMediaId)->delete();

                    // Hapus file fisik lama
                    if (!empty($oldCoverPath) && file_exists(FCPATH . $oldCoverPath)) {
                        unlink(FCPATH . $oldCoverPath);
                    }
                }

                // Insert relasi baru
                $db->table('blog_post_media')->insert([
                    'post_id'  => $id,
                    'media_id' => $newMediaId,
                    'type'     => 'cover'
                ]);

                // Update cover_image di blog_posts dengan path baru
                $postData['cover_image'] = $newFullPath;
            }

            // Update post
            if (!$this->blogPostModel->update($id, $postData)) {
                throw new \Exception(implode(', ', $this->blogPostModel->errors()));
            }

            // Update kategori
            $db->table('blog_post_category')->where('post_id', $id)->delete();
            $db->table('blog_post_category')->insert([
                'post_id'     => $id,
                'category_id' => $categoryId
            ]);

            // Update tag (sync)
            $db->table('blog_post_tag')->where('post_id', $id)->delete();
            if (!empty($tagIds)) {
                $tagsData = [];
                foreach ($tagIds as $tagId) {
                    $tagsData[] = ['post_id' => $id, 'tag_id' => $tagId];
                }
                $db->table('blog_post_tag')->insertBatch($tagsData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Terjadi kesalahan saat menyimpan perubahan.');
            }

            return redirect()->to(site_url('admin/posts'))->with('success', 'Berita berhasil diperbarui!');
        } catch (\Exception $e) {
            $db->transRollback();

            // Jika upload baru gagal, hapus file yang sudah terupload
            if ($uploadNewImage && isset($newFullPath) && file_exists(FCPATH . $newFullPath)) {
                unlink(FCPATH . $newFullPath);
            }

            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function updateStatus($id)
    {
        $json = $this->request->getJSON();
        $status = $json->status;

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

            return redirect()->back()->with('success', 'Postingan berhasil dihapus.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
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

        $db         = \Config\Database::connect();
        $uploadPath = 'uploads/blog/';
        $fileName = $file->getRandomName();
        $fullPath   = $uploadPath . $fileName;

        try {
            if (!$file->move(FCPATH . $uploadPath, $fileName)) {
                throw new \RuntimeException('Gagal mengupload gambar. Silakan coba lagi.');
            }

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
