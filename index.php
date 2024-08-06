<?php
if (!count($_POST) && !count($_GET) && !file_exists(__DIR__.'/install.lock') && file_exists(__DIR__.'/install.php'))
{
	// Запускаем инсталлятор, если нет лок файла
	header('Location: install.php');
	die();
}
if (PHP_VERSION_ID < 70000)
{
	die('PHP 7.0.0 or newer is required. Your PHP version is '.PHP_VERSION.' ('.PHP_VERSION_ID.'). Please ask your host to upgrade PHP.');
}

require_once __DIR__.'/include.php';
Menu::$op = isset($_GET['op']) ? $_GET['op'] : 'main';    // Не менять на ?? чтобы была возможность вывести сообщение о старой версии PHP
Plugins::OnCreate();
Index::CheckVKLogin();
if (Index::CheckAdminConfig())
{
	include main_path.'pages/config.php';
	echo '		</div>
            </div><div class="box-content">            
        ';
	Index::Footer();
	die();
}
if (Index::Header())
{
	switch (Menu::$op)
	{
		case 'upload': include('pages/upload.php'); die();
		case 'act': include('pages/action.php'); die();
	}
}
if (defined('error'))
{
	echo error;
}
if (!Auth::$need_auth) {
	if (Index::Page())
	{
		$cur_menu = Menu::getCurMenuItem();
		if ($cur_menu !== false && $cur_menu['show_method'] !== false)
		{
			$cur_menu['show_method']();
		} else {
			include main_path . 'pages/' . Menu::$op . '.php';
		}
		echo '
                </div>
            </div><!--/span-->
        </div><!--/row-->';
	}
} else {
    Index::Auth();
}
Index::Footer();