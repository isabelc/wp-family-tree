	function add_drag() {
		var el = document.getElementById('dragableElement');
		var leftEdge = el.parentNode.clientWidth - el.clientWidth;
		var topEdge = el.parentNode.clientHeight - el.clientHeight;
		var dragObj = new dragObject(el, null, new Position(leftEdge, topEdge), new Position(0, 0));
	}

	function AddOnload(myfunc)
	{
		if(window.addEventListener)
			window.addEventListener('load', myfunc, false);
		else if(window.attachEvent)
			window.attachEvent('onload', myfunc);
	}
	
//	AddOnload(familytreemain);
//	AddOnload(add_drag);

	