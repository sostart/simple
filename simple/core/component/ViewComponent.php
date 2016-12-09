<?php

namespace core\component;

class ViewComponent
{
	public function init()
	{
		$this->view_path = APP_PATH.'view'.DIRECTORY_SEPARATOR;
		$this->layout_path = $this->view_path.'_Layout_'.DIRECTORY_SEPARATOR;
		$this->layout_file = $this->layout_path.'main.php';
	}

	public function setLayout($file,$path=false)
	{
		$this->layout_file = $this->layout_path.$file.'.php';
	}

	public function layout( $__view_file__, $__view_data__=array(), $__view_display__=true, $__view_path__=false )
	{
		$__content__ = $this->render($__view_file__, $__view_data__, false, $__view_path__);
		
		extract($__view_data__,EXTR_OVERWRITE);

		if($__view_display__)
		{
			require($this->layout_file);
		}
		else
		{
			ob_start();
			ob_implicit_flush(false);
			require($this->layout_file);
			return ob_get_clean();
		}
	}
	
	public function render( $__view_file__, $__view_data__=array(), $__view_display__=true, $__view_path__=false )
	{
		if( !$__view_path__ ) $__view_path__ = APP_PATH;

		if( !is_file($__view_file__) )
		{
			if( $__view_file__[0]=='/' || $__view_file__[0]=='.' )
			{
				$__view_file__ = $__view_path__.'view'.str_replace(array('\\','/'),DIRECTORY_SEPARATOR,$__view_file__).'.php';	
			}
			else
			{
				$__view_file__ = $__view_path__.'view'.DIRECTORY_SEPARATOR.
					\Controller::getController().DIRECTORY_SEPARATOR.
					str_replace(array('\\','/'),DIRECTORY_SEPARATOR,$__view_file__).'.php';
			}
		}
		
		if(is_file($__view_file__))
		{
			extract($__view_data__,EXTR_OVERWRITE);

			if($__view_display__)
			{
				require($__view_file__);
			}
			else
			{
				ob_start();
				ob_implicit_flush(false);
				require($__view_file__);
				return ob_get_clean();
			}
		}
	}

	public function path()
	{
		return $this->view_path;
	}
}