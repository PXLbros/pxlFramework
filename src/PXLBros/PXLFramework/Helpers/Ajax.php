<?php namespace PXLBros\PXLFramework\Helpers;

class Ajax
{
	private $ui;

	private $data =
	[
		'data' => [],
		'redirect' => NULL,
		'notification' => NULL,
		'error' => NULL
	];

	private $data_keys = [];

	function __construct(UI $ui)
	{
		$this->ui = $ui;

		$this->data_keys = array_keys($this->data);
	}

	public function assign($key, $value)
	{
		if ( in_array($key, $this->data_keys) )
		{
			throw new \Exception('Key "' . $key . '" is a restricted keyword.');
		}

		$this->data['data'][$key] = $value;
	}

	public function setError($error_message)
	{
		$this->data['error'] = $error_message;
	}

	public function redirect($url, $delay_in_ms = 0)
	{
		$this->data['redirect'] =
		[
			'url' => $url,
			'delay' => $delay_in_ms
		];

		return $this->output();
	}

	public function showSuccess($text)
	{
		$this->data['notification'] =
		[
			'text' => $text,
			'type' => UI::NOTIFICATION_TYPE_SUCCESS
		];
	}

	public function showInfo($text)
	{
		$this->data['notification'] =
		[
			'text' => $text,
			'type' => UI::NOTIFICATION_TYPE_INFO
		];
	}

	public function showWarning($text)
	{
		$this->data['notification'] =
		[
			'text' => $text,
			'type' => UI::NOTIFICATION_TYPE_WARNING
		];
	}

	public function showError($text)
	{
		$this->data['notification'] =
		[
			'text' => $text,
			'type' => UI::NOTIFICATION_TYPE_ERROR
		];
	}

	public function output($cookie = NULL)
	{
		ob_start();

		if ( $this->data['redirect'] !== NULL && $this->data['notification'] !== NULL )
		{
			$this->ui->setNotification($this->data['notification']['text'], $this->data['notification']['type']);

			$this->data['notification'] = NULL;
		}

		$response = \Response::json($this->data);

		if ( $cookie !== NULL )
		{
			$response->withCookie($cookie);
		}

		return $response;
	}

	public function outputWithError($error, $show_notification = false)
	{
		$this->setError($error);

		if ( $show_notification === true )
		{
			$this->showError($error);
		}

		return $this->output();
	}
}