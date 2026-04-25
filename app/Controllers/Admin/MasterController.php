<?php

namespace App\Controllers\Admin;

use App\Models\CategoryModel;
use App\Models\TagModel;

class MasterController extends BaseAdminController
{
    protected $tagModel, $categoryModel;

    public function __construct()
    {
        $this->tagModel = new TagModel();
        $this->categoryModel = new CategoryModel();
    }

    // Tags related methods
    public function tagsIndex()
    {
        return $this->renderAdmin("pages/admin/tags", [
            'title'            => 'Master Tag',
            'pageTitle'        => 'Master Tag',
            'pageDescription'  => 'Halaman tag sudah tersedia. Pada tahap berikutnya kita bisa tambahkan list tag, pencarian, serta aksi tambah dan ubah.',
            'hasAction'        => true,
            'isActionModal'    => true,
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Master Tag', 'url' => null],
            ],
            'tagsDataUrl'     => site_url('/admin/tags-data'),
        ]);
    }

    public function tagsData()
    {
        $nameFilter = $this->request->getGet('name');
        $query = $this->tagModel->select('id, name');

        if (!empty($nameFilter)) {
            $query->like('name', $nameFilter);
        }

        return $this->response->setJSON([
            'data' => $query->orderBy('id', 'DESC')->findAll()
        ]);
    }

    public function tagsCreate()
    {
        $name = $this->request->getPost('name');

        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|is_unique[blog_tags.name]',
                'errors' => [
                    'required' => 'Nama tag wajib diisi.',
                    'min_length' => 'Nama tag wajib lebih dari 3 huruf.',
                    'is_unique' => 'Nama tag tidak valid atau sudah ada.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $message = reset($errors); // Get the first error message
            return redirect()->back()->withInput()->with('warning', $message);
        }

        try {
            $this->tagModel->insert(['name' => $name]);
            return redirect()->back()->with('success', 'Tag baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function tagsUpdate($id)
    {
        $name = $this->request->getPost('name');

        $rules = [
            'name' => [
                'rules' => "required|min_length[3]|is_unique[blog_tags.name,id,{$id}]",
                'errors' => [
                    'required' => 'Nama tag wajib diisi.',
                    'min_length' => 'Nama tag wajib lebih dari 3 huruf.',
                    'is_unique' => 'Nama tag tidak valid atau sudah ada.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $message = reset($errors);
            return redirect()->back()->withInput()->with('warning', 'Update Gagal: ' . $message);
        }

        try {
            $this->tagModel->update($id, ['name' => $name]);
            return redirect()->back()->with('success', 'Tag berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function tagsDelete($id)
    {
        try {
            if ($this->tagModel->delete($id)) {
                return redirect()->back()->with('success', 'Tag berhasil dihapus.');
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }

    // Categories related methods
    public function categoriesIndex()
    {
        return $this->renderAdmin("pages/admin/categories", [
            'title'            => 'Master Kategori',
            'pageTitle'        => 'Master Kategori',
            'pageDescription'  => 'Halaman kategori sudah tersedia. Pada tahap berikutnya kita bisa tambahkan list kategori, pencarian, serta aksi tambah dan ubah.',
            'hasAction'        => true,
            'isActionModal'    => true,
            'breadcrumbs'      => [
                ['title' => 'Dashboard', 'url' => site_url('/admin')],
                ['title' => 'Master Kategori', 'url' => null],
            ],
            'categoriesDataUrl'     => site_url('/admin/categories-data'),
        ]);
    }

    public function categoriesData()
    {
        $nameFilter = $this->request->getGet('name');
        $query = $this->categoryModel->select('id, name');

        if (!empty($nameFilter)) {
            $query->like('name', $nameFilter);
        }

        return $this->response->setJSON([
            'data' => $query->orderBy('id', 'DESC')->findAll()
        ]);
    }

    public function categoriesCreate()
    {
        $name = $this->request->getPost('name');

        $rules = [
            'name' => [
                'rules' => 'required|min_length[3]|is_unique[blog_categories.name]',
                'errors' => [
                    'required' => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori wajib lebih dari 3 huruf.',
                    'is_unique' => 'Nama kategori tidak valid atau sudah ada.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $message = reset($errors);
            return redirect()->back()->withInput()->with('warning', $message);
        }

        try {
            $this->categoryModel->insert(['name' => $name]);
            return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data.');
        }
    }

    public function categoriesUpdate($id)
    {
        $name = $this->request->getPost('name');

        $rules = [
            'name' => [
                'rules' => "required|min_length[3]|is_unique[blog_categories.name,id,{$id}]",
                'errors' => [
                    'required' => 'Nama kategori wajib diisi.',
                    'min_length' => 'Nama kategori wajib lebih dari 3 huruf.',
                    'is_unique' => 'Nama kategori tidak valid atau sudah ada.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $message = reset($errors);
            return redirect()->back()->withInput()->with('warning', 'Update Gagal: ' . $message);
        }

        try {
            $this->categoryModel->update($id, ['name' => $name]);
            return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data.');
        }
    }

    public function categoriesDelete($id)
    {
        try {
            if ($this->categoryModel->delete($id)) {
                return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
            }
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data.');
        }
    }
}