/**
 * 
 * Author: Sunil Shah
 * (c) Copyright 2010 Sunil Shah
 * (c) Copyright 2010 Esscotti Ltd
 * 
 */
<?php
	require_once("../../../wp-blog-header.php");
	require_once('class.tree.php');
	require_once('wpft_options.php');
?>
(function () {
	
	function Rect() {
		this.x = 0;
		this.y = 0;
		this.width = -1;
		this.height = -1;
	};
	
	var m_vAllNodes = new Array();

	var canvasbgcol = "<?php echo wpft_options::get_option('canvasbgcol'); ?>";
	var nodeoutlinecol = "<?php echo wpft_options::get_option('nodeoutlinecol'); ?>";
	var nodefillcol	= "<?php echo wpft_options::get_option('nodefillcol'); ?>";
	var nodefillopacity = <?php echo wpft_options::get_option('nodefillopacity'); ?>;
	var nodetextcolour = "<?php echo wpft_options::get_option('nodetextcolour'); ?>";
	
	var	m_iFontLineHeight 	= 0,
		m_iFontLineDescent 	= 0,
		m_yLine				= 0,
		m_iInterBoxSpace 	= 10,
		m_iBoxBufferSpace 	= 2,
		BOX_Y_DELTA			= 20,
		BOX_LINE_Y_SIZE		= 100,
		iMaxHoverPicHeight	= 150,
		iMaxHoverPicWidth	= 150,
		aCurrentHoverPic	= null,
		aFamilyTreeElement 	= null;
	

	var bOneNamePerLine 		= true,
		bOnlyFirstName 			= false,
		bBirthAndDeathDates 	= true,
		bConcealLivingDates 	= true,
//		bDeath 					= true,
		bSpouse 				= true,
		bMaidenName 			= true,
		bGender					= true,
		bDiagonalConnections	= false;

	var m_Canvas,
		m_CanvasRect;
	
	var iCanvasWidth = 100,
		iCanvasHeight = 100;
	
	this.familytreemain = function() {
		m_Canvas 	=  Raphael("familytree", iCanvasWidth, iCanvasHeight);
        m_Canvas.clear();
		m_CanvasRect = m_Canvas.rect(0, 0, iCanvasWidth, iCanvasHeight, 10).attr({fill: canvasbgcol, stroke: "none"}).toBack();

        aFamilyTreeElement = document.getElementById("familytree");
        text_sStartName = document.getElementById("focusperson");
//        text_sStartName.onkeydown = onKeyDown_Name; 
        hoverpic = document.getElementById("hoverimage");
        
        createTreeFromArray(tree_txt);
        loadImages();
        loadShortInfo();
        loadLongInfo();
        redrawTree();
	};

	this.redrawTree = function() {
		text_sStartName.value = text_sStartName.value.replace("\n", "");
		text_sStartName.value = text_sStartName.value.replace("\n", "");
		var sPerson = text_sStartName.value;
		var n = find(sPerson);		// Node n
		if (n == null) {
//			alert("Sorry, \'"+sPerson + "\' is not part of the tree");
			return;
		}
		iCanvasWidth = 100;
		iCanvasHeight = 100;
        m_Canvas.clear();
        freeNodesAllocatedTexts();
		m_CanvasRect = m_Canvas.rect(0, 0, iCanvasWidth, iCanvasHeight, 10).attr({fill: canvasbgcol, stroke: "none"}).toBack();
        printTreeFromNode(sPerson);
	};

	function Node(sID) {
		var m_sFTID			= sID,
			m_sName			= "?",
			m_sImageURL		= null,
			m_HoverPic		= null,
			m_sShortInfoURL	= null,
			m_sLongInfoURL	= null,
			m_sMaiden		= null,
			m_iBirthYear	= -1,
			m_sGender 		= null,
			m_nSpouseNode	= null,
			m_iMyBranchWidth = 0;
		
		var m_vParents		= new Array(), 		// Guess this will be 0, 1 or 2 nodes only
			m_vChildren		= new Array();
		var	m_MyRect 		= new Rect(),		// bounding box for this node
			m_BothRect 		= new Rect();		// bounding box for this node + spouse node
		var m_RaphRect,							// Raphael's graphics box on canvas
			m_RaphTexts		= new Array();
		
		var	m_sBirthday		= null;
		var	m_sDeathday		= null;
		
		m_vAllNodes.push(this);		// Java.add()  -->  Javascript.push()		
		
		this.setSpouse = function(sID) {
			var nSpouse = findOrCreate(sID);
			connectSpouses(this, nSpouse);
		};
		
/*		this.setSpouseName = function(sName) {
			var nSpouse = findOrCreateName(sName);
			connectSpouses(this, nSpouse);
		};*/

		this.setMaiden = function(sMaidenName) {
			m_sMaiden = sMaidenName;
		};


/*		this.addChild = function(sChildName) {
			var nChild = findOrCreate(sChildName);
			connectParentChild(this, nChild);
			return nChild;
		}; */
		
		this.addParent = function(sParentID) {
			var nParent = findOrCreate(sParentID);
			connectParentChild(nParent, this);
			return nParent;
		};
		
/*		this.addParentName = function(sParentName) {
			var nParent = findOrCreateName(sParentName);
			connectParentChild(nParent, this);
			return nParent;
		};*/
		
		this.setBirthYear = function(iYear) {
			m_iBirthYear 	= iYear;
		};

		this.setBirthday = function(sDate) {	//"Birthday=19780213"
			m_sBirthday 	= sDate;
		};

		this.setDeathday = function(sDate) {	//"Deathday=19780213"
			m_sDeathday 	= sDate;
		};

		this.getBirthday = function() {	//"Birthday=19780213"
			return m_sBirthday;
		};

		this.getDeathday = function() {	//"Deathday=19780213"
			return m_sDeathday;
		};

		this.setGender = function(bIsFemale) {
			m_sGender 	= bIsFemale ? "f" : "m";
		};
				
		this.setImageURL = function(sURL) {
			m_sImageURL 	= sURL;
			m_HoverPic		= new Image();
		};
		
		this.setShortInfoURL = function(sURL) {
			m_sShortInfoURL	= sURL;
		};
		
		this.setLongInfoURL = function(sURL) {
			m_sLongInfoURL 	= sURL;
		};
		
		this.getRaphRect = function() {
			return this.m_RaphRect;
		};
		
		this.getRaphTexts = function() {
			return this.m_RaphTexts;
		};
		
		this.setRaphTexts = function(arr) {
			this.m_RaphTexts = arr;
		};
		
		this.getFTID = function() {
			return m_sFTID;
		};
		
		this.setFTID = function(sID) {
			this.m_sFTID = sID;
		};
		
		this.getName = function() {
			return m_sName;
		};
		
		this.setName = function(sName) {
			m_sName = sName;
		};
		
		this.getImageURL = function() {
			return m_sImageURL;
		};
		
		this.getImage = function() {
			return m_HoverPic;
		};
		
		this.getShortInfoURL = function() {
			return m_sShortInfoURL;
		};
		
		this.getLongInfoURL = function() {
			return m_sLongInfoURL;
		};
		
		this.getChildren = function() {
			return m_vChildren;
		};
		
		this.getParents = function() {
			return m_vParents;
		};

		this.getMyRect = function() {
			return m_MyRect;
		};

		this.getBothRect = function() {
			return m_BothRect;
		};

		this.getSpouseName = function() {
			return m_nSpouse == null ? "" : m_nSpouse.getName();
		};
		
		//private int countParentGenerations() {
		this.countParentGenerations = function() {
			var iCurrentDepth = 0;
			for (i in this.m_vParents) {
				var p = this.m_vParents[i]; 
				iCurrentDepth = Math.max(iCurrentDepth, p.countParentGenerations());
			}
			return 1+iCurrentDepth;
		};
		

		//private int countChildrenGenerations() {
		this.countChildrenGenerations = function() {
			var iCurrentDepth = 0,
				iNumChildren = m_vChildren.length;
			for (i in m_vChildren) {
				iCurrentDepth = Math.max(iCurrentDepth, m_vChildren[i].countChildrenGenerations());
			}
			if (iNumChildren != 0)
				return 1+iCurrentDepth;
			else
				return 0;
		};
		
		this.calcParentBranchWidths = function() {
			/*
			 * TODO
			 */
			return 0;
		};
		
		
		this.calcChildrenBranchWidths = function() {
			this.getMeAndSpousesGraphBoxes();
			var iMyWidth = m_iInterBoxSpace + m_BothRect.width;

			this.m_iMyBranchWidth = 0;
			for (i in m_vChildren) {
				this.m_iMyBranchWidth += m_vChildren[i].calcChildrenBranchWidths();
			}
			
			if (iMyWidth > this.m_iMyBranchWidth)
				this.m_iMyBranchWidth = iMyWidth;
			
//			System.out.println("Width at "+getName()+" is "+this.m_iMyBranchWidth);
			
			return this.m_iMyBranchWidth;		
		};

		this.graphMe = function(iRelativePos, iGeneration) {
			var iY = this.getBoxY(iGeneration),
				iX = iRelativePos;
			iX += this.m_iMyBranchWidth/2;
			this.getGraphBox(iX, iY);
		};
		
		this.graphChildren = function(iRelativePos, iGeneration) {
			var iTotWidth = 0;
			for (i in m_vChildren) {
				var n = m_vChildren[i];
				iTotWidth += n.m_iMyBranchWidth;
			}

			var iY = this.getBoxY(iGeneration),
				iX = iRelativePos-iTotWidth/2;
			
			for (i in m_vChildren) {
				var n = m_vChildren[i];
				iX += n.m_iMyBranchWidth/2;
				m_vChildren[i].getGraphBox(iX, iY);
				m_vChildren[i].graphChildren(iX, 1+iGeneration);
				iX += n.m_iMyBranchWidth/2;
			}
		};

		this.graphConnections = function() {
			var	iXFrom, iYFrom, iXTo, iYTo, iYMid;
			
			if (bSpouse && this.m_nSpouse != null)
				iXFrom = m_MyRect.x+m_MyRect.width;
			else
				iXFrom = m_MyRect.x+m_MyRect.width/2;
			iYFrom = m_MyRect.y+m_MyRect.height+1;
			
			for (i in m_vChildren) {
				var n = m_vChildren[i];
				iXTo = n.getMyRect().x + n.getMyRect().width/2;
				iYTo = n.getMyRect().y;
				iYMid = (iYFrom+iYTo)/2;
				
				if (bDiagonalConnections) 
					drawLine(iXFrom, iYFrom, iXTo, iYTo);

				else {
					drawLine(iXFrom, iYFrom, 	iXFrom, iYMid);
					drawLine(iXFrom, iYMid,		iXTo, 	iYMid);
					drawLine(iXTo,	 iYMid, 	iXTo, 	iYTo);
				}
				n.graphConnections();
			}

		};
		
		this.getBoxY = function(iRow) {
			return iRow*BOX_LINE_Y_SIZE + BOX_Y_DELTA;
			
	/*		if (iRow == 0)
				return BOX_Y_DELTA;
			
			Node nParent = m_vParents.get(0);
			if (iRow == 1)
				return nParent.m_MyRect.y + nParent.m_MyRect.height + BOX_Y_DELTA;
			
			Node nGrandParent = nParent.m_vParents.get(0);
			
			int iLargestY = 0;
			int iNumChildren = nGrandParent.m_vChildren.size();
			for (int i = 0; i < iNumChildren; ++i) {
				Node n = nGrandParent.m_vChildren.get(i);
				if ((n.m_MyRect.y+n.m_MyRect.height) > iLargestY)
					iLargestY = n.m_MyRect.y+n.m_MyRect.height;
			}
			return iLargestY+BOX_Y_DELTA;
	*/
		};
		
		/*
		 * Calculates the size of this node, the spouses nodes, and total, without printing them
		 */
		this.getMeAndSpousesGraphBoxes = function() {
			
			this.getGraphBox(0, 0); 		// set m_MyRect	
//			System.out.println("Got my rectwidth ("+getName()+") = "+m_MyRect.width);
			m_BothRect.x 		= m_MyRect.x;
			m_BothRect.y 		= m_MyRect.y;
			m_BothRect.width 	= m_MyRect.width;
			m_BothRect.height 	= m_MyRect.height;
			
			if (bSpouse && (this.m_nSpouse != null)) {
				this.m_nSpouse.getGraphBox(0, 0);	// set m_nSpouse.m_MyRect
//				System.out.println("Got spouses rectwidth ("+m_nSpouse.getName()+") = "+m_nSpouse.m_MyRect.width);
				var iLargestHeight = Math.max(m_MyRect.height, this.m_nSpouse.getMyRect().height);
				m_BothRect.width += this.m_nSpouse.getMyRect().width;
				m_BothRect.height = iLargestHeight;
				m_MyRect.height = iLargestHeight;
				this.m_nSpouse.getMyRect().height = iLargestHeight;
			}
			
//			if (m_MyRect.height > m_iLargestBoxHeight)
//				m_iLargestBoxHeight = m_MyRect.height;
		};
				
		/*
		 * Calculate the size of this node and print it
	 	 * Formatting flags: bOneNamePerLine bOnlyFirstName bBirthAndDeathDates bDeath bSpouse bMaidenName bShowGender bDiagonalLines		
		 */
		this.getGraphBox = function(X, Y) {
			var bPrint = (X != 0) || (Y != 0);
			var r = new Rect();
			resetLine();	// Which line our "cursor" is on while printing in box.

			if (!bPrint) {
				m_MyRect.width = 0;
				m_MyRect.height = 0;
				m_BothRect.width = 0;
				m_BothRect.height = 0;
			}
			r.x = m_MyRect.x;
			r.y = m_MyRect.y;
			r.width = m_MyRect.width;
			r.height = m_MyRect.height;
			if (bPrint) {
				this.m_RaphRect = m_Canvas.rect();
				r.x = X-m_BothRect.width/2;
				r.y = Y;
				growCanvas(r.x+r.width, r.y+r.height+1);
				this.m_RaphRect.attr({	
										"x": r.x,
										"y": r.y,
										"width": r.width,
										"height": r.height+1,
										r: 4
								});
				this.m_RaphRect.attr({stroke: nodeoutlinecol, fill: nodefillcol, "fill-opacity": nodefillopacity});
//				m_Canvas.rect(r.x, r.y, r.width, r.height+1, 4)
//							.attr({stroke: "#ff0", fill: "#0ff", "fill-opacity": .4
				this.m_RaphRect.show();
				this.m_RaphRect.click(function () {
					var n = findRectOwningNode(this);
					if (n != null) {
						text_sStartName.value = n.getName();
						redrawTree();
					}
                }).mouseover(function (ev) {
                    this.animate({"fill-opacity": .75}, 300);
					var n = findRectOwningNode(this);
					if (n != null) {
						var im = n.getImage();
						if (im != null) {
							hoverpic.src = encodeURI(n.getImageURL());
							hoverpic.width = im.width;
							hoverpic.height = im.height;
							hoverpic.style.left =  (ev.clientX+20) + 'px';
							hoverpic.style.top = (ev.clientY-10) + 'px';
							hoverpic.style.visibility="visible";
						}
					}                    
                }).mouseout(function () {
                	hoverpic.style.visibility="hidden";
                    this.animate({"fill-opacity": nodefillopacity}, 300);
                });

//				System.out.println("Box corner for "+getName()+" = "+r.x+","+r.y+" w="+r.width+" h="+r.height);
			}
			
			var sGender = (bGender 		&& (m_sGender != null)) ? " ("+m_sGender+")" : "";
			var sMaiden = (bMaidenName 	&& (m_sMaiden != null)) ? " ("+m_sMaiden+")" : "";
			
			if (bOnlyFirstName) {
				// split full name into list of single names
				sTokens = this.getName().split(" ");
				r = makeGraphBox(bPrint, r, sTokens[0] + sGender, this);

			} else {
				if (bOneNamePerLine) {
					
					sTokens = this.getName().split(" ");
					for (var i = 0; i < sTokens.length; ++i) {
						if (i == sTokens.length-1)
							r = makeGraphBox(bPrint, r, sTokens[i] + sGender, this);
						else
							r = makeGraphBox(bPrint, r, sTokens[i], this);
							
					}

					if (bMaidenName && (m_sMaiden != null))
						r = makeGraphBox(bPrint, r, sMaiden, this);
					
				} else {
					r = makeGraphBox(bPrint, r, this.getName() + sGender + sMaiden, this);	
				}
				
			}
			
			if (bBirthAndDeathDates) {
				var birth = this.getBirthday() != null ? this.getBirthday() : "", 
					death = this.getDeathday() != null ? this.getDeathday() : ""; 
				if (death != "" || (birth != "" && !bConcealLivingDates))
					r = makeGraphBox(bPrint, r, "("+birth+"-"+death + ")", this);	
			}
			
//			if (bDeath) { }
			
			// final box size adjustments
			r.height += 3;
			r.width += 2;
			
//			if (!bPrint) {
				m_MyRect.x 		= r.x;	// Save the size of this node's box
				m_MyRect.y 		= r.y;
				m_MyRect.width 	= r.width;
				m_MyRect.height = r.height;
//			}

			if (bPrint && bSpouse && (this.m_nSpouse != null)) {
				// Print this node's spouse
				bSpouse = false;	// so we don't get an infinite loop
				this.m_nSpouse.getGraphBox(r.x+r.width /*+m_nSpouse.m_MyRect.width/2 */, r.y);
				bSpouse = true;
			}
		};
		
		this.setImage = function(img) {
			m_HoverPic = img;
		};
		
	}	// End of 'Node' class declaration
	

	
	// File global name space
	
	function findRectOwningNode(rect) {
		for (arr in m_vAllNodes) {
			var anode = m_vAllNodes[arr],
				bnode = anode.getRaphRect(); 
			if (bnode != null) {
				if (bnode == rect)
					return anode;
			}
		}
		return null;
	}
	
	function findTextOwningNode(textobj) {
		for (arr in m_vAllNodes) {
			var anode = m_vAllNodes[arr];
			var	texts = anode.getRaphTexts();

			for (var j in texts) {	
				if (texts[j] == textobj) 
					return anode;
			}
		}
		return null;
	}
	
	function growCanvas(w, h) {
		iCanvasWidth = Math.max(w+2, iCanvasWidth+2);
		iCanvasHeight= Math.max(h+2, iCanvasHeight+2);
		m_Canvas.setSize(iCanvasWidth, iCanvasHeight);
		m_CanvasRect.attr({
			x: 0, 
			y: 0, 
			width: iCanvasWidth, 
			height: iCanvasHeight, 
			r: 10}).attr({fill: canvasbgcol, stroke: "none"}).toBack();
	}
	
	function drawLine(iXFrom, iYFrom, 	iXTo, iYTo) {
		m_Canvas.path(
				"M"+iXFrom+" "+iYFrom+
				"L"+iXTo+" "+iYTo);
							
	}


	function find(sFTID) {
		var n;
		for (var i = 0; i < m_vAllNodes.length; ++i) {
			n = m_vAllNodes[i];
			if (n.getFTID().toLowerCase() == sFTID.toLowerCase())
				return n;
		}
		return null;
	}

	function findName(sName) {
		var n;
		for (var i = 0; i < m_vAllNodes.length; ++i) {
			n = m_vAllNodes[i];
			if (n.getName() != null)
				if (n.getName().toLowerCase() == sName.toLowerCase())
					return n;
		}
		return null;
	}

	// Find, or else create, a Node based on a Family Tree ID
	function findOrCreate(sFTID) {
		if (sFTID == null)
			return null;
		
		var nFound = find(sFTID);
		
		if (nFound == null) {
			nFound = new Node(sFTID);
		}
		
		return nFound;
	}
	
	// Find, or else create, a Node based on a Family Tree ID
	function findOrCreateName(sName) {
		var nFound = findName(sName);
		
		return findOrCreate(findName(sName));
	}

	function connectParentChild(p, c) {	// connect Node p (parent) and Node c (child)
		var bFound = false;
		var ch = p.getChildren();
		for (i in ch) {
			if (ch[i] == c) {
				bFound = true;
			}
		}
		if (bFound == false)
			ch.push(c);
		
		
//		if (!p.m_vChildren.contains(c))
//			p.m_vChildren.push(c);	
//		if (!c.m_vParents.contains(p))
//			c.m_vParents.push(p);
		
		bFound = false;
		var pa = c.getParents();
		for (i in pa) {
			if (pa[i] == p) {
				bFound = true;
			}
		}
		if (bFound == false)
			pa.push(p);
	}
	
	function connectSpouses(s1, s2) {	// connect Nodes s1 and s2
		if (s1.m_nSpouse == null)
			s1.m_nSpouse = s2;
		else if (s1.m_nSpouse != s2)
			alert("Error in tree: "+s1.getName()+" already has spouse "+s1.m_nSpouse.getName()+". Not connecting "+s2.getName());
		
		if (s2.m_nSpouse == null)
			s2.m_nSpouse = s1;
		else if (s2.m_nSpouse != s1)
			alert("Error in tree: "+s2.getName()+" already has spouse "+s2.m_nSpouse.getName()+". Not connecting "+s1.getName());
	}
	
	function printTreeFromNode(sID) {
		var n = find(sID);		// Node n
	
		// get metrics from the graphics
//X		m_FontMetrics = g.getFontMetrics(g.getFont());
		// get the height of a line of text in this font and render context
		m_iFontLineHeight = 10;	//X m_FontMetrics.getHeight();
		m_iFontLineDescent = 4;	//X m_FontMetrics.getDescent();
		
		if (n == null) {
			alert("Sorry, \'"+sID + "\' is not part of the tree");
			return;
		}
		
		// Where to draw a node depends on the total size of its branches
		n.countParentGenerations();
		n.countChildrenGenerations();
		n.calcParentBranchWidths();		// TODO
		n.calcChildrenBranchWidths();
		
//		m_iLargestBoxHeight = 0;
//X		m_CurrentGraphics.setColor(Color.white);	// drawing colour
		
		// Draw graph boxes
		n.graphMe(0, 0);
		n.graphChildren(n.m_iMyBranchWidth/2, 1);
		
		// Draw interconnect lines
		n.graphConnections();
	}
	
	function getPixelsPerLine() {
		return 14;//m_iFontLineHeight+m_iFontLineDescent;
	}

	function resetLine() {
		m_yLine = 0;
	}
	
	function getLine() {
		return m_yLine;
	}
	
	function incLine() {
		++m_yLine;
	}
	
	/*
	 * makeGraphBox()
	 * 
	 * Calculate or Print graph boxes.
	 * If bPrintIt is true, the box rectangle (theBox) isn't touched. It stays intact.
	 */
	function makeGraphBox(bPrintIt, theBox, sAddString, node) {
		// get the advance of my text in this font and render context
				
		if (bPrintIt) {
			var w = 0;
			var theRaphText = m_Canvas.text(0, 0, sAddString != null ? decodeURI(sAddString) : "");
//			theRaphText.attr({"font": '24px "Helvetica Neue", Helvetica, "Arial Unicode MS", Arial, sans-serif '});
			theRaphText.attr({"fill": nodetextcolour});
			node.getRaphTexts().push(theRaphText);
			
			w = theRaphText.getBBox().width;
			w = 0;	//X Remove this line if Java!
			theRaphText.attr({
				x: theBox.x + (theBox.width - w)/2 + m_iBoxBufferSpace, 
				y: theBox.y + m_iFontLineHeight + getLine()*getPixelsPerLine()
			}).toFront();
			
			theRaphText.click(function () {
				var n = findTextOwningNode(this);
				if (n != null) {
					text_sStartName.value = n.getName();
					redrawTree();
				}
            }).mouseover(function (ev) {
				var n = findTextOwningNode(this);
				if (n != null) {
					var r = n.getRaphRect();
					var im = n.getImage();
	                r.animate({"fill-opacity": .75}, 300);
					if (im != null) {
						hoverpic.src = encodeURI(n.getImageURL());
						hoverpic.width = im.width;
						hoverpic.height = im.height;
						hoverpic.style.left =  (ev.clientX+20) + 'px';
						hoverpic.style.top = (ev.clientY-10) + 'px';
						hoverpic.style.visibility="visible";
					}
				}
            }).mouseout(function () {
            	hoverpic.style.visibility="hidden";
	
				var n = findTextOwningNode(this);
				if (n != null) {
					var r = n.getRaphRect();
	                r.animate({"fill-opacity": .4}, 300);
				}
            });
			
			incLine();

		} else {
			var w = 0;
			if (sAddString != null) {
				var temptxt = m_Canvas.text(0, 0, decodeURI(sAddString));
//				temptxt.attr({"font": '24px "Helvetica Neue", Helvetica, "Arial Unicode MS", Arial, sans-serif '});
//				temptxt.attr({"color": '#0f0'});
				w = temptxt.hide().getBBox().width;
				temptxt.remove();
			}
			w += 2*m_iBoxBufferSpace+1;
			if (w > theBox.width)
				theBox.width = w;
	
			theBox.height += getPixelsPerLine();
		}
		
		return theBox;
	}
	
	function isValidDate(sDate) {
		var dlen = sDate.length;
		
		if ((dlen != 4) && (dlen != 6) && (dlen != 8))
			return false;
		
		
	}
	
	function createTreeFromArray(sArray) {
       
		var n = null,
			sKey = null;
        for (var i in sArray) {
        	var sLine = sArray[i];
        	var sTokens = sLine.split("=");

			if ((sTokens.length < 1) || (sTokens[0].charAt(0) == '#'))
				continue;
			
			sKey = sTokens[0].toLowerCase();
			if (sKey == "esscottiftid") {
				n = findOrCreate(sTokens[1]);
				
			} else if (sKey == "name") {
				n.setName(sTokens[1]);

			} else if (sKey == "imageurl") {
				n.setImageURL(sTokens[1]);
				
			} else if (sKey == "shortinfourl") {
				n.setShortInfoURL(sTokens[1]);
				
			} else if (sKey == "longinfourl") {
				n.setLongInfoURL(sTokens[1]);
				
			} else if (sKey == "male") {
				n.setGender(false);
				
			} else if (sKey == "female") {
				n.setGender(true);
				
			} else if (sKey == "spouse") {	// ID!
				n.setSpouse(sTokens[1]);
				
//bad			} else if (sKey == "spousename") {	// by name - less secure
//bad				n.setSpouseName(sTokens[1]);
				
			} else if (sKey == "maiden") {
				n.setMaiden(sTokens[1]);
				
			} else if (sKey == "year") {	// obsolete
				n.setBirthYear(sTokens[1]);
				
			} else if (sKey == "birthday") {
				n.setBirthday(sTokens[1]);
				
			} else if (sKey == "deathday") {
				n.setDeathday(sTokens[1]);
				
			} else if (sKey == "parent") {	// ID!
				n.addParent(sTokens[1]);
				
//bad			} else if (sKey == "parentname") {	// by name - less secure
//bad				n.addParentName(sTokens[1]);
				
			} else if (sKey == "child") {	// obsolete
				n.addChild(sTokens[1]);


			} else
				alert("Error in family tree file: " + sTokens[0]+" "+sTokens[1]);
		}
	}	
	
	function freeNodesAllocatedTexts() {
		for (i in m_vAllNodes) {
			m_vAllNodes[i].setRaphTexts(null);
			m_vAllNodes[i].setRaphTexts(new Array());
		}
	}
	
	function loadImages() {
		for (i in m_vAllNodes) {
			var n = m_vAllNodes[i];
			var sUrl = n.getImageURL();
			if (sUrl == null)
				continue;
			var img = new Image();
			n.setImage(img);
//			aFamilyTreeElement.appendChild(img);
			img.style.visibility="hidden";
			img.src = encodeURI(sUrl);
			img.onload = function() {
			    var max_height = iMaxHoverPicHeight;
			    var max_width = iMaxHoverPicWidth;

			    var height = this.height;
			    var width = this.width;
			    var ratio = height/width;

			    // If height or width are too large, they need to be scaled down
			    // Multiply height and width by the same value to keep ratio constant
			    if (height > max_height)
			    {
			        ratio = max_height / height;
			        height = height * ratio;
			        width = width * ratio;
			    }

			    if (width > max_width)
			    {
			        ratio = max_width / width;
			        height = height * ratio;
			        width = width * ratio;
			    }

			    this.width = width;
			    this.height = height;
			};
		}
	}
	
	function loadShortInfo() {
		for (var i in m_vAllNodes) {
			var n = m_vAllNodes[i];
			var sUrl = n.getShortInfoURL();
			if (sUrl == null)
				continue;
			
			
		}
	};
	
	function loadLongInfo() {
		for (var i in m_vAllNodes) {
			var n = m_vAllNodes[i];
			var sUrl = n.getLongInfoURL();
			if (sUrl == null)
				continue;
			
			
		}
	};

/*	function addTip(node, txt) {
	    $(node).mouseenter(function() {
	       tipText = txt;
	       tip.fadeIn();
	       over = true;
	    }).mouseleave(function() {
	       tip.fadeOut(200);
	       over = false;
	    });
	};
*/
	this.setMaxHoverPicWidth = function(iWidth) 	{ iMaxHoverPicWidth = iWidth; 	};
	this.setMaxHoverPicHeight= function(iHeight) 	{ iMaxHoverPicHeight = iHeight; };

	this.setOneNamePerLine = function(bState) 		{ bOneNamePerLine = bState; 		redrawTree(); };
	this.setOnlyFirstName = function(bState) 		{ bOnlyFirstName = bState; 		redrawTree(); };
	this.setBirthAndDeathDates = function(bState) 	{ bBirthAndDeathDates = bState; 	redrawTree(); };
	this.setConcealLivingDates = function(bState)		 	{ bConcealLivingDates = bState; 	redrawTree(); };
	this.setDeath = function(bState) 				{ bDeath = bState; 				redrawTree(); };
	this.setSpouse = function(bState) 				{ bSpouse = bState; 				redrawTree(); };
	this.setMaidenName = function(bState) 			{ bMaidenName = bState; 			redrawTree(); };
	this.setGender = function(bState) 				{ bGender = bState; 				redrawTree(); };
	this.setDiagonalConnections = function(bState)	{ bDiagonalConnections = bState; 	redrawTree(); };
	this.getOneNamePerLine = function() 			{ return bOneNamePerLine; 		};
	this.getOnlyFirstName = function() 				{ return bOnlyFirstName; 		};
	this.getBirthAndDeathDates = function() 		{ return bBirthAndDeathDates; 	};
	this.getConcealLivingDates = function() 		{ return bConcealLivingDates; 	};
	this.getDeath = function() 						{ return bDeath; 				};
	this.getSpouse = function() 					{ return bSpouse; 				};
	this.getMaidenName = function() 				{ return bMaidenName; 			};
	this.getGender = function() 					{ return bGender; 				};
	this.getDiagonalConnections = function() 		{ return bDiagonalConnections; };


	this.onFocusPersonChanged = function(e) {
//		value = value.replace("\n", "");

		//var strippedString:String=oldString.split("\n").join(" ");
		
/* TODO Get this to work!
 * 
 * 		if (!e) var e = window.event;
		if (e.keyCode) code = e.keyCode;
		else if (e.which) code = e.which;

		if (code == 13)
			redrawTree();*/
	};
<?php
	$tree_data_js = "var tree_txt = new Array(\n";	
	$the_family = tree::get_tree();
	$first = true;
	foreach ($the_family as $node) {
		if (!$first) {
			$tree_data_js .= ',';
		} else {
			$first = false;
		}
//		$str  = '"EsscottiFTID='.$node->post_id.'",'."\n";
		$str  = '"EsscottiFTID='.$node->name.'",'."\n";
		$str .= '"Name='.$node->name.'",'."\n";
//		"ImageURL=http://4.bp.blogspot.com/_O5UyIvnogNQ/SfGnEbDHJ9I/AAAAAAAAAMg/v7k5o6ziL3c/s1600/image-upload-211-713263.jpg",
//		"ShortInfoURL=http://bpa.esscotti.com/",
//		"LongInfoURL=http://cpa.esscotti.com/",
		$str .= '"'.(($node->gender=='m')?'Male':'Female').'",'."\n";
		$str .= '"Birthday='.$node->born.'",'."\n";
//		"Spouse=Sunil Shah",
		$str .= '"Parent='.$the_family[$node->mother]->name.'",'."\n";
		$str .= '"Parent='.$the_family[$node->father]->name.'"'."\n";

		$tree_data_js .= $str;	


	}
	$tree_data_js .= ');'."\n";

	echo $tree_data_js;
?>
	var tree_txt_old = new Array(
		"EsscottiFTID=Susanna Ideberg",
		"Name=Susanna Ideberg",
		"ImageURL=http://4.bp.blogspot.com/_O5UyIvnogNQ/SfGnEbDHJ9I/AAAAAAAAAMg/v7k5o6ziL3c/s1600/image-upload-211-713263.jpg",
		"ShortInfoURL=http://bpa.esscotti.com/",
		"LongInfoURL=http://cpa.esscotti.com/",
		"Female",
		"Birthday=19780213",
		"Spouse=Sunil Shah",
		"Parent=Gunnel Ideberg",
		"Parent=Lennart Ideberg",
		
		"EsscottiFTID=Sunil Shah",
		"Name=Sunil Shah",
		"Male",
		"Birthday=19670819",
		"ImageURL=http://1.bp.blogspot.com/_O5UyIvnogNQ/SfGnOj3rI4I/AAAAAAAAAMo/v9sbMJdEFFM/s1600/image-upload-204-754629.jpg",
		"Spouse=Susanna Ideberg",
		"Parent=Vikram Shah",
		"Parent=Gullan Shah",
		
		"EsscottiFTID=Kelvin Shah",
		"Name=Kelvin Alexander Ideberg Shah",
		"Male",
		"ImageURL=http://1.bp.blogspot.com/_O5UyIvnogNQ/TBj8ic3hD8I/AAAAAAAAAOY/mM1A3_CZPkg/s1600/image-upload-57-729426.jpg",
		"Birthday=20070707",
		"Parent=Susanna Ideberg",
		"Parent=Sunil Shah",
		
		"EsscottiFTID=Amanda Shah",
		"Name=(Lilly) Amanda Ideberg Shah",
		"Female",
		"ImageURL=http://4.bp.blogspot.com/_O5UyIvnogNQ/S_JSaV9a4CI/AAAAAAAAAOQ/vvNzRiD4ab8/s1600/image-upload-155-709151.jpg",
		"Birthday=20090204",
		"Parent=Sunil Shah",
		"Parent=Susanna Ideberg",
		
		"EsscottiFTID=Gunnel Ideberg",
		"Name=Gunnel Ideberg",
		"Maiden=Däldborg",
		"Spouse=Lennart Ideberg",
		"Female",
		"Year=1947",
		"Parent=Evert Däldborg",
		"Parent=Lilly Däldborg",
		
		"EsscottiFTID=Helena Ideberg",
		"Name=Helena Ideberg",
		"Female",
		"Year=1980",
		"Spouse=Thomas Beckman",
		"Parent=Gunnel Ideberg",
		"Parent=Lennart Ideberg",
		
		"EsscottiFTID=Vikram Shah",
		"Name=Vikram Shah",
		"Spouse=Gullan Shah",
		"Male",
		"Birthday=19350919",
		"Parent=Jivanlal Shah",
		"Parent=Kalavati Shah",
		
		"EsscottiFTID=Gullan Shah",
		"Name=Gullan Shah",
		"Spouse=Vikram Shah",
		"Maiden=Wetterbrandt",
		"Female",
		"BIrthday=19450313",
		"Parent=Gunnar Wetterbrandt",
		"Parent=Margit Wetterbrandt",
		
		"EsscottiFTID=Margit Wetterbrandt",
		"Name=Margit Wetterbrandt",
		"ImageURL=http://www.esscotti.com/images/esscotti/business-meeting-200x300.jpg",
		"Female",
		"Spouse=Gunnar Wetterbrandt",
		
		"EsscottiFTID=Emma Holtz",
		"Name=Emma Holtz",
		"ImageURL=http://www.esscotti.com/images/esscotti/oak-197x300.jpg",
		"Maiden=Shah",
		"Spouse=Fredrik Holtz",
		"Female",
		"Year=1969",
		"Parent=Vikram Shah",
		"Parent=Gullan Shah",
		
		"EsscottiFTID=Ingemar Wetterbrandt",
		"Name=Ingemar Wetterbrandt",
		"Spouse=Yvonne Wetterbrandt",
		"Year=1950",
		"Parent=Gunnar Wetterbrandt",
		"Parent=Margit Wetterbrandt",
		
		"EsscottiFTID=Pål Wetterbrandt",
		"Name=Pål Wetterbrandt",
		"imageURL=http://media.jnytt.se/resources/news/2010-05-14171502-PalWetterbrandtAranas_small_600.jpg",
		"Birthday=19860303",
		"Parent=Ingemar Wetterbrandt",
		"Parent=Yvonne Wetterbrandt",
		
		"EsscottiFTID=Per Wetterbrandt",
		"Name=Per Wetterbrandt",
		"Birthday=19840412",
		"Parent=Ingemar Wetterbrandt",
		"Parent=Yvonne Wetterbrandt",
		
		"EsscottiFTID=Lilly Däldborg",
		"Name=Lilly Däldborg",
		"Female",
		"Year=1923",
		"Maiden=Johansson",
		"Spouse=Evert Däldborg",
		
		"EsscottiFTID=Evert Däldborg",
		"Name=Evert Däldborg",
		"Male",
		"Deathday=20050101",
		"Maiden=Johansson",
		"Spouse=Lilly Däldborg",
		
		"EsscottiFTID=Tore Däldborg",
		"Name=Tore Däldborg",
		"Spouse=Anette Früh",
		"Parent=Lilly Däldborg",
		"Parent=Evert Däldborg",
		
		"EsscottiFTID=Jan Däldborg",
		"Name=Jan Däldborg",
		"Spouse=Lena Däldborg",
		"Parent=Lilly Däldborg",
		"Parent=Evert Däldborg",
		
		"EsscottiFTID=Mattias Däldborg",
		"Name=Mattias Däldborg",
		"Parent=Jan Däldborg",
		"Parent=Lena Däldborg",
		
		"EsscottiFTID=Linda Däldborg",
		"Name=Linda Däldborg",
		"Parent=Jan Däldborg",
		"Parent=Lena Däldborg",
		
		"EsscottiFTID=Arvind Shah",
		"Name=Arvind Shah",
		"Spouse=Sally Kitchin",
		"ImageURL=http://www.esscotti.com/images/esscotti/last-piece-of-puzzle.jpg",
		"Year=1973",
		"Parent=Vikram Shah",
		"Parent=Gullan Shah",
		
		"EsscottiFTID=Emma Shah",
		"Name=Emma <?php echo 'OAPA'; ?>Hira Marie Shah",
		"Parent=Arvind Shah",
		"EsscottiFTID=Max Shah",
		"Name=Max Stephen Shah",
		"Parent=Arvind Shah",
		"EsscottiFTID=Enzo Shah",
		"Name=Enzo Alexander Shah",
		"Parent=Arvind Shah",

		"EsscottiFTID=Adam",
		"Name=Adam",
		"Spouse=Anna",
		
		"EsscottiFTID=Bertil",
		"Name=Bertil",
		"Spouse=Beata",
		"Parent=Adam",
		
		"EsscottiFTID=Cesar",
		"Name=Cesar",
		"Spouse=Cissi",
		"Parent=Bertil",
		
		"EsscottiFTID=David",
		"Name=David",
		"Spouse=Diana",
		"Parent=Cesar",
		
		"EsscottiFTID=Erik",
		"Name=Erik",
		"Spouse=Emma",
		"Parent=David",
		
		"EsscottiFTID=Filip",
		"Name=Filip",
		"Spouse=Fatima",
		"Parent=Erik",
		
		"EsscottiFTID=Gustav",
		"Name=Gustav",
		"Spouse=Gunilla",
		"Parent=Filip"
	);

})();
