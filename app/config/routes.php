<?php
// Thêm các routes cho quản lý phương thức thanh toán
$router->add('/payment-methods', 'PaymentMethodsController', 'index');
$router->add('/payment-methods/add-bank-account', 'PaymentMethodsController', 'addBankAccount');
$router->add('/payment-methods/add-payment-card', 'PaymentMethodsController', 'addPaymentCard');
$router->add('/payment-methods/edit-bank-account/:id', 'PaymentMethodsController', 'editBankAccount');
$router->add('/payment-methods/edit-payment-card/:id', 'PaymentMethodsController', 'editPaymentCard');
$router->add('/payment-methods/delete-bank-account/:id', 'PaymentMethodsController', 'deleteBankAccount');
$router->add('/payment-methods/delete-payment-card/:id', 'PaymentMethodsController', 'deletePaymentCard');

// Routes cho ExpensesController
$router->add('/expenses', 'ExpensesController', 'index');
$router->add('/expenses/create', 'ExpensesController', 'create');
$router->add('/expenses/edit/:id', 'ExpensesController', 'edit');
$router->add('/expenses/update/:id', 'ExpensesController', 'update');
$router->add('/expenses/update', 'ExpensesController', 'update');
$router->add('/expenses/delete', 'ExpensesController', 'delete');

// Routes cho trang chi tiết chi tiêu - đảm bảo routes này đúng và có độ ưu tiên cao
// $router->add('/expenses/detail/:id', 'ExpensesController', 'viewExpense');
// $router->add('/expenses/view/:id', 'ExpensesController', 'viewExpense');

// Routes cho ExpenseCategoriesController
$router->add('/expense-categories', 'ExpenseCategoriesController', 'index');
$router->add('/expense-categories/create', 'ExpenseCategoriesController', 'create');
$router->add('/expense-categories/edit/:id', 'ExpenseCategoriesController', 'edit');
$router->add('/expense-categories/delete/:id', 'ExpenseCategoriesController', 'delete');
$router->add('/expense-categories/duplicate/:id', 'ExpenseCategoriesController', 'duplicate');
$router->add('/expense-categories/import-samples', 'ExpenseCategoriesController', 'importSamples');
