<?php

namespace App\Controllers\Admin;

use App\Models\AdminDashboardModel;
use CodeIgniter\HTTP\ResponseInterface;

class DashboardController extends BaseAdminController
{
    public function index(): string
    {
        return $this->renderAdmin('pages/admin/dashboard', [
            'title'            => 'Dashboard',
            'pageTitle'        => 'Dashboard',
            'pageDescription'  => 'Ringkasan cepat untuk memantau isi blog dan memulai pekerjaan dari satu panel yang rapi.',
            'dashboardDataUrl' => site_url('/admin/dashboard-data'),
        ]);
    }

    public function data(): ResponseInterface
    {
        $dashboardModel = new AdminDashboardModel();
        $overview       = $dashboardModel->getOverview();

        return $this->response->setJSON([
            'stats'       => admin_dashboard_stat_cards($overview),
            'recentPosts' => admin_dashboard_recent_posts($dashboardModel->getRecentPosts()),
            'adminPages'  => admin_dashboard_pages(),
        ]);
    }
}
