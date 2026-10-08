<?php
use WecodeGuy\ProjetMyShop\Controllers\{HomeController, ProductController, AuthController, CartController, OrderController};
use WecodeGuy\ProjetMyShop\Controllers\Admin\{DashboardController, ProductAdminController, CategoryAdminController, UserAdminController, OrderAdminController};

/** @var \WecodeGuy\ProjetMyShop\Core\Router $router */

// ---- Public ----
$router->get('/',               [HomeController::class, 'index']);
$router->get('/home',           [HomeController::class, 'index']);
$router->get('/product/{id}',   [ProductController::class, 'show']);

// ---- Authentification ----
$router->get('/signin',         [AuthController::class, 'showSignin']);
$router->post('/signin',        [AuthController::class, 'signin']);
$router->get('/signup',         [AuthController::class, 'showSignup']);
$router->post('/signup',        [AuthController::class, 'signup']);
$router->post('/logout',        [AuthController::class, 'logout']);
$router->get('/profile',        [AuthController::class, 'showProfile']);
$router->post('/profile',       [AuthController::class, 'updateProfile']);

// ---- Panier & commandes ----
$router->get('/cart',           [CartController::class, 'index']);
$router->post('/cart/add',      [CartController::class, 'add']);
$router->post('/cart/update',   [CartController::class, 'update']);
$router->post('/cart/remove',   [CartController::class, 'remove']);
$router->get('/checkout',       [OrderController::class, 'checkout']);
$router->post('/checkout',      [OrderController::class, 'place']);
$router->get('/orders',         [OrderController::class, 'index']);
$router->get('/orders/{id}',    [OrderController::class, 'show']);

// ---- Administration ----
$router->get('/admin',                          [DashboardController::class, 'index']);

$router->get('/admin/products',                 [ProductAdminController::class, 'index']);
$router->get('/admin/products/create',          [ProductAdminController::class, 'create']);
$router->post('/admin/products',                [ProductAdminController::class, 'store']);
$router->get('/admin/products/{id}/edit',       [ProductAdminController::class, 'edit']);
$router->post('/admin/products/{id}',           [ProductAdminController::class, 'update']);
$router->post('/admin/products/{id}/delete',    [ProductAdminController::class, 'delete']);

$router->get('/admin/categories',               [CategoryAdminController::class, 'index']);
$router->post('/admin/categories',              [CategoryAdminController::class, 'store']);
$router->post('/admin/categories/{id}/delete',  [CategoryAdminController::class, 'delete']);

$router->get('/admin/users',                    [UserAdminController::class, 'index']);
$router->post('/admin/users/{id}/toggle-admin', [UserAdminController::class, 'toggleAdmin']);

$router->get('/admin/orders',                   [OrderAdminController::class, 'index']);
$router->get('/admin/orders/{id}',              [OrderAdminController::class, 'show']);
$router->post('/admin/orders/{id}/status',      [OrderAdminController::class, 'updateStatus']);
