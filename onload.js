	function AddOnload(myfunc)
	{
		if(window.addEventListener)
			window.addEventListener('load', myfunc, false);
		else if(window.attachEvent)
			window.attachEvent('onload', myfunc);
	}
	
	AddOnload(familytreemain);
	