<?php
namespace WecodeGuy\ProjetMyShop\Controllers\Admin;

use WecodeGuy\ProjetMyShop\Models\Order;

class OrderAdminController extends AdminController
{
    public function index(): void
    {
        $this->view('admin/orders/index', [
            'title' => 'Commandes', 'section' => 'orders',
            'orders' => (new Order())->all(), 'statuses' => Order::STATUSES,
        ]);
    }

    public function show(string $id): void
    {
        $orders = new Order();
        $order = $orders->find((int)$id);
        if (!$order) {
            $this->notFound('Commande introuvable.');
            return;
        }
        $this->view('orders/show', [
            'title' => 'Commande n°' . $order['id'], 'section' => 'orders', 'order' => $order,
            'items' => $orders->items((int)$order['id']), 'statuses' => Order::STATUSES,
            'backUrl' => '/admin/orders', 'adminMode' => true,
        ]);
    }

    public function updateStatus(string $id): void
    {
        $status = $this->post('status');
        if (isset(Order::STATUSES[$status])) {
            (new Order())->setStatus((int)$id, $status);
            $this->flash('success', 'Statut mis à jour.');
        } else {
            $this->flash('error', 'Statut invalide.');
        }
        redirect('/admin/orders/' . (int)$id);
    }
}
