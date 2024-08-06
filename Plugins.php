<?php

abstract class Plugins
{

	use tList;

	public static $class_namespace = 'Plugins';

	/**
	 * Вызывает метод ActionProcess у всех доступных плагинов
	 * @param int $n    Номер действия
	 * @param int $num  Номер персонажа
	 * @param array $post_data  Массив с данными для запроса, частично заполняется автоматически
	 * @param string $return_page   Название page для возврата после запроса
	 * @param bool $no_return       Ставить true, если не нужно выполнять редирект на $return_page (для всяких админских ajax действий)
	 * @return bool Вернуть true в случае нахождения нужного номера
	 */
	public static function ActionProcess(int $n, int $num, array &$post_data, string &$return_page, bool &$no_return): bool
	{
		$result = false;
		self::CallMethod('ActionProcess', true, static function(array $data) use (&$result): bool
		{
			$result = $data['result'];
			return $result;
		}, $n, $num, $post_data, $return_page, $no_return);
		return $result;
	}

	/**
	 * Обработчик запросов к серверу со страниц (обычно он делается перед выводом данных)
	 * @param array $post_data Массив с данными, передаваемыми на сервер
	 * @return void
	 */
	public static function pageCurlProcess(array &$post_data)
	{
		self::CallMethod('pageCurl', true, static function(): bool
		{
			return false;
		}, $post_data);
	}

	/**
	 * Вставка HTML кода на страницу
	 * @param string $position Место вызова метода: top - в начале страницы, bottom - в конце (нужно проверять перед возвратом данных, т.к. метод вызывается два раза)
	 * @param mixed $data Данные от CURL запроса серверу (есть не на всех страницах)
	 * @return void
	 */
	public static function pageInjectHTML(string $position, $data)
	{
		$result = '';
		self::CallMethod('pageInjectHTML', true, static function($param) use (&$result): bool
		{
			$result .= $param['result'];
			return false;
		}, $position, $data);
		echo $result;
	}

	/**
	 * Добавляет кнопки действия на персонаже
	 * @param array $val Массив с данными текущего персонажа
	 * @param string $confirm Кусок js кода с вызовом функции подтверждения действия (старый костыль)
	 * @return string
	 */
	public static function addPersActionButton(array $val, string $confirm): string
	{
		$result = '';
		self::CallMethod('addPersActionButton', true, static function($param) use (&$result): bool
		{
			$result .= $param['result'];
			return false;
		}, $val, $confirm);
		return $result;
	}

	/**
	 * Обработчик для крона
	 * @param string $action
	 * @return void
	 */
	public static function cronProcess(string $action)
	{
		self::CallMethod('cronProcess', true, static function($param): bool
		{
			if ($param['result'])
			{
				return true;
			}
			return false;
		}, $action);
	}

    /**
     * Вызов метода OnCreate для возможности добавления обработчиков до проверки авторизации
     * @return void
     */
    public static function OnCreate()
    {
        self::CallMethod('OnCreate', true, static function(): bool
        {
            return false;
        });
    }

	/**
	 * Первичная инициализация плагинов
	 * @return void
	 */
	public static function InitMenuAndPages()
	{
		self::CallMethod('InitMenuAndPages', true, static function(): bool
		{
			return false;
		});
	}

	/**
	 * Инициализация для добавления вкладок в настройки и ссылок для крона
	 * @return void
	 */
	public static function InitConfigs()
	{
		self::CallMethod('InitConfigs', true, static function(): bool
		{
			return false;
		});
	}

	/**
	 * Обрабатываем админские ajax запросы в плагинах
	 * @return void
	 */
	public static function AjaxAdmin()
	{
		self::CallMethod('AjaxAdmin', true, static function(): bool
		{
			return false;
		});
	}

	/**
	 * Обрабатываем ajax запросы игроков в плагинах
	 * @return void
	 */
	public static function AjaxPlayer()
	{
		self::CallMethod('AjaxPlayer', true, static function(): bool
		{
			return false;
		});
	}

	/**
	 * Проверка целостности класса
	 * @param string $type Тип плагина
	 * @param string $name Название класса
	 * @return string
	 */
	public static function Validate(string $type, string $name): string
	{
		if (!in_array($type, ['Ratings', 'Plugins', 'Merchants'], true))
		{
			throw new RuntimeException('Unknown Augmentation Type: '.$type);
		}
		$file_name = main_path.'classes/'.$type.'/'.$name.'.php';
		if (!file_exists(($file_name)))
		{
			throw new RuntimeException('Class not found: '.$type.'/'.$name);
		}
		return md5(str_replace(["\r","\n","\t", ' '], '', preg_replace('/public \$alexdnepro_validate_key = \'.+\';/', '', file_get_contents($file_name))));
	}

}