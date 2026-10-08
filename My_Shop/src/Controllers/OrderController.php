<?php
namespace WecodeGuy\ProjetMyShop\Controllers;

use WecodeGuy\ProjetMyShop\Core\{Auth, Cart};
use WecodeGuy\ProjetMyShop\Models\Order;

class OrderController extends Controller
{
    public function checkout(): void
    {
        Auth::requireLogin();
        $cart = Cart::details();
        if (!$cart['lines']) {
            $this->flash('error', 'Votre panier est vide.');
            redirect('/cart');
        }
        $this->view('orders/checkout', ['title' => 'Validation de la commande'] + $cart);
    }

    public function place(): void
    {
        Auth::requireLogin();
        $cart = Cart::details();           // prix relus en base : on ne fait jamais confiance au client
        if (!$cart['lines']) {
            redirect('/cart');
        }
        $orderId = (new Order())->create((int)Auth::id(), $cart['lines']);
        Cart::clear();
        $this->flash('success', "Commande n°$orderId enregistrée. Merci !");
        redirect('/orders/' . $orderId);
    }

    public function index(): void
    {
        Auth::requireLogin();
        $this->view('orders/index', ['title' => 'Mes commandes', 'orders' => (new Order())->forUser((int)Auth::id()), 'statuses' => Order::STATUSES]);
    }

    public function show(string $id): void
    {
        Auth::requireLogin();
        $orders = new Order();
        $order  = $orders->find((int)$id);
        // Un client ne voit que SES commandes
        if (!$order || (int)$order['user_id'] !== Auth::id()) {
            $this->notFound('Commande introuvable.');
            return;
        }
        $this->view('orders/show', [
            'title' => 'Commande n°' . $order['id'], 'order' => $order,
            'items' => $orders->items((int)$order['id']), 'statuses' => Order::STATUSES, 'backUrl' => '/orders',
        ]);
    }
}
