<?php

  # ----------------------------------------------------------------

	function print_r2($array) {

		print("<pre>"); print_r($array); print("</pre>");
	}

	# ----------------------------------------------------------------

	function get_r2($rs) {

		ob_start();

		print ("<pre>");
		print_r ($rs);
		print ("</pre>");

		$content = ob_get_contents();
		ob_end_clean();

		return($content);
	}

	# ----------------------------------------------------------------

	function print_r2_adv ($rs) {

		ob_start();
		print_r ($rs);
		$content = ob_get_contents();
		ob_end_clean();

		//$content = htmlentities( $content);
		$content = htmlentities( $content, ENT_COMPAT, 'UTF-8' );
		$content = str_replace(' ', '&nbsp;', $content);

		print '<code>' . nl2br($content) . '</code>';

	}

	# ----------------------------------------------------------------

	function get_r2_adv($rs) {

		ob_start();
		print_r ($rs);
		$content = ob_get_contents();
		ob_end_clean();

		$content = htmlentities( $content );
		$content = str_replace(' ', '&nbsp;', $content);
		$content = str_replace("\n", '<br />', $content);

		return $content;
	}

	function print_r2_button($msg = ' Pre ') {
		print '<button type="button" class="btn btn-primary" onclick="';
		print 'p=this.nextSibling;if(p.style.height==\'0px\'){p.style.height=h;}else{h=p.style.height;p.style.height=\'0px\'}">';
		print $msg;
		print '</button>';
	}
	# ----------------------------------------------------------------

	function redirect($page,$delay=0) {
		print("<META HTTP-EQUIV=\"Refresh\" CONTENT=\"$delay;URL=$page\">\n");

	}

	# ----------------------------------------------------------------

	function debugbox($boxname, $content) {

		$r = mt_rand(0,1000000);
		$boxname_r = $boxname . '_' . $r;

		print "<table width='100%' style='border: #505050 DOTTED 1px;'><tr><td> ";

		print "DebugBox: <a href='javascript:void(0)' onClick=\"javascript:d = document.getElementById('".$boxname_r."');if(d.style.display == 'none') {d.style.display = 'inline';} else {d.style.display = 'none'}\">".$boxname."</a>";

		print "<div width='100%' bgcolor='white'><tr><td id = '". $boxname_r ."' style='display:none;'>$content</div>";


		print("</td></tr></table>");
	}

# ------------------------------------------------------------------------------------

	function debugTree($obj, $name='nn', $showlevel=0, $level=0) {
		$t= "\n\n";
	    $utf8 = 1;
	    $width = array('50', '50', '95%');

		$colors = array('object'   => '#ffffdd',
						'array'    => '#ffeebb',
						'string'   => '#ddffdd',
						'NULL'     => '#eeeeee',
						'boolean'  => '#ffdddd',
						'integer'  => '#aadddd',
						'double'   => '#ffee77'
						);


		$rnd = mt_rand(1,999999);

		switch ( gettype( $obj ) ) {

		  case 'string':

		    if( $utf8 == 1 ) {
		        $child = htmlentities($obj, ENT_COMPAT, 'UTF-8');
		    } else {
		        $child = htmlentities($obj);
		    }
			break;

		  case 'boolean':

			$tmp = $obj ? 'true' : 'false';
			$child = $tmp;
			break;

		  case 'integer':

			$child = intval( $obj );
			break;

		  case 'double':
			$child = floatval( $obj );
			break;

		  case 'object':

		  	//--

		  	#print('<hr size=1>');
		  	#print_r( get_object_vars($obj) );
		  	//--

			$child = '';
			if( count(get_object_vars($obj)) == 0) {
			    $trl = '<tr><td>(empty object) &nbsp; class: '. get_class($obj) .'</td></tr>';
			} else {
			    $trl = '<tr><td><a href="javascript:void(0)" onClick="javascript:d = document.getElementById(\''. $rnd. '\'); if(d.style.display == \'none\') {d.style.display = \'\'; } else {d.style.display = \'none\'};"><nobr>[ + ]</nobr></a>  &nbsp; class: '. get_class($obj) .' </td></tr>';
			}
			$tr = '';
			foreach($obj as $k => $v) {

				$n = debugTree($v, $k, $showlevel, $level+1);
				if(is_array($n)) {

					if (isset($colors[$n[1]])) {
						$ccolor = $colors[$n[1]];
					}
					else {
						$ccolor = "#FFFFFF";
					}

					$tr .= '<tr>';
					foreach($n as $nk => $nv) {
						$tr .= '<td style="background-color: '. $ccolor .'" width="'. $width[$nk] .' valign="top">'. $nv . '</td>';
					}
					$tr .= '</tr>' . "\n";
				}
			}

			$child .= '<table class="debugTree" border=0 width="100%" style="border: solid #aaa; border-width: 1px 0 0 1px;">' ."\n";
			$child .= $trl;

			if($level < $showlevel) {
				$displaymode = '';
			} else {
				$displaymode = 'none';
			}

			$child .= '<tr><td valign=top><table class="debugTree" border=0  width="100%" style="display:'. $displaymode .'" id="'. $rnd .'">';
            		$child .= '<colgroup><col width="100"><col width="100"><col><colgroup>' ."\n";
			$child .= $tr;
			$child .= '</table></td></tr>';
			//('. $showlevel .' vs '. $level .')

			$child .= '</table>';

			break;

		  case 'array':

			$child = '';
			if(empty($obj)) {
				$trl = '<tr><td>(empty)</td></tr>';
			} else {
				$trl = '<tr><td><a href="javascript:void(0)" onClick="javascript:d = document.getElementById(\''. $rnd. '\'); if(d.style.display == \'none\') {d.style.display = \'\'; } else {d.style.display = \'none\'};">[ + ]</a></td></tr>';
			}
			$tr = '';
			foreach($obj as $k => $v) {

				$n = debugTree($v, $k, $showlevel, $level+1);
				if(is_array($n)) {

					if (isset($colors[$n[1]])) {
						$ccolor = $colors[$n[1]];
					}
					else {
						$ccolor = "#FFFFFF";
					}

					$tr .= '<tr>';
					foreach($n as $nk=>$nv) {
						$tr .= '<td style="background-color: '. $ccolor .'" width="'. $width[$nk] .'" valign="top">'. $nv . '</td>';
					}
					$tr .= '</tr>' . "\n";
				}
			}

			$child .= '<table class="debugTree" border=0 width="100%" class="debugTree" style="border: solid #aaa; border-width: 1px 0 0 1px;">' ."\n";


			$child .= $trl;

			if($level < $showlevel) {
				$displaymode = '';
			} else {
				$displaymode = 'none';
			}

			$child .= '<tr><td valign=top><table class="debugTree" border=0  width="100%" style="display:'. $displaymode .'" id="'. $rnd .'">';
            $child .= '<colgroup><col width="100"><col width="100"><col><colgroup>' ."\n";
			$child .= $tr;
			$child .= '</table></td></tr>';
			//('. $showlevel .' vs '. $level .')

			$child .= '</table>';

			break;

		default:

			$child = 'n/a: (' .  gettype ( $obj ) . ')' . $obj;
		}
		$type = gettype ( $obj );

		if($level == 0) {

			$t .= '<table class="debugTree" style="border: solid #999 1px;display:;" width="100%" border=0>' ."\n";

			$t .= '<tr><td width=50 valign=top>'. $name.'</td><td width=50 valign=top>'. $type .'</td><td width="95%">'. $child .'</td>' ."\n";

			$t .= '</table>' . "\n";

		} else {

			return array($name, $type, $child);
		}

		$t .= "\n\n";
		return $t;
	}

    # ------------------------------------------------------------------------------------

    function debugTree2($obj, $name='debugTree', $showlevel=0, $level=0) {
    global $g_debugTree;

    	$t = '';
    	$utf8 = 1;// fix to $g_debugTree['$utf8']=x


    	$type = gettype ( $obj );

		switch ( $type ) {

		  case 'string':

		    if( $utf8 == 1 ) {
		        $child = htmlentities($obj, ENT_COMPAT, 'UTF-8');
		    } else {
		        $child = htmlentities($obj);
		    }
			break;

		  case 'boolean':

			$tmp = $obj ? 'true' : 'false';
			$child = $tmp;
			break;

		  case 'integer':

			$child = intval( $obj );
			break;

		  case 'double':
			$child = floatval( $obj );
			break;

		  case 'object':

			$child = '[object]'; //tmp
			break;

		  case 'object':

			$child = '[object]'; //tmp
			break;

		} //switch

		if($level == 0) {

				$t .= '<div class="debugTree">' . "\n";
				$t .= '<div class="debugTreeLeft">left</div><div class="debugTreeRight">right</div>' . "\n";
				$t .= '</div>' . "\n";
		} else {

			return array($name, $type, $child);
		}

		return $t;

   	}

   	# ------------------------------------------------------------------------------------
   	function print_logs($logs, $filter=array(), $mode='long' ) {

    	print ("<div align='center'>");
    	print ("<table width='92%' bgcolor='#f0f0f0' style='border: SOLID 1px #909090'><tr><td>\n");

    	if(is_array($logs) && (count($logs)>0) ) {

    		foreach($logs as $log) {
    			print (" <tr>\n");

    			if($mode == 'long') {
    				foreach($log as $data) {
    					print("    <td>". $data . "</td>\n");
    				}
    			}

    			if($mode == 'short') {
    				$msg = $log[0];
    				$comb = basename($log['file']) . ':' . $log['line'];
    				$rest = $log['function'];
    				if(isset($log['class']) && !empty($log['class']) )  { $rest = $log['class'] . '::' . $rest; }

    				
    				print("    <td>". $comb . "</td>\n");
    				print("    <td>". $rest . "</td>\n");

    				print("    <td>". $msg .  "</td>\n");
    			}

    			print (" </tr>\n");
    		}
    	}

    	print ("</table>\n");
    	print ("</div>\n");
   	}

    # ------------------------------------------------------------------------------------

    function SQL_error($query, $file, $line, $function = '', $class = '') {

        $db = svgFramework::Get("db");

    	print ("<div align='center'>");
    	print ("<table width='92%' bgcolor='#f0f0f0' style='border: SOLID 1px #909090'><tr><td>\n");

    		print ("SQL Error:<br>" . $db -> ErrorMsg() . "<br /><br />\n");
    		print ("Query:<br><pre>\n". str_replace("\t", '  ', $query ). "</pre><br /><br />\n");
    		print ("File: <b>$file</b><br />\n");
    		print ("Line: <b>$line</b><br />\n");
    		if($function != '')
    			print ("Function: <b>$function</b><br />\n");

    		if ($class != '')
    			print ("Class: <b>$class</b><br />\n");

    	print ("</td></tr>\n");
    	print (" <tr><td><a href=\"#\" onClick=\"document.getElementById('sql-error-backtrace').style.display='table';return false;\">show debug_backtrace</a></td></tr>\n");
    	print (" <tr><td id=\"sql-error-backtrace\" style=\"display: none;\">".  debugTree(debug_backtrace(), 'debug_backtrace', 2) ."</td></tr>\n");
    	print ("</table>\n");
    	print ("</div>\n");

    	#print debugTree(debug_backtrace(), 'debug_backtrace', 0);

    	##print_r2( debug_backtrace() );
	}

	#------------------------------------------------------------------------------

	function datatable($data) {

		print get_datatable($data);
	}

	#------------------------------------------------------------------------------

	function get_datatable($data) {

		$tt = 0;
		$c = '';
		$alt = 0;

		//fix prescan for keys

		$titles = array();
		foreach($data as $line) {

                        $ntitles = array();
                        if(is_array($line)) {
                            $ntitles = array_keys($line);
                            $titles = array_unique(array_merge($titles, $ntitles));
                        }

			#dbg: print('>>');print_r($ntitles); print('<br>');
			#dbg: print('::');print_r($titles); print('<br>');
		}



		$c .= '<table class="datatable" xwidth="100%">' ."\n";
		foreach($data as $lineKey => $line) {

			if($tt==0) {
				/*
				$titles = @array_keys($line);
				if(is_array($titles)) {
					$titles = array_keys($line);
				} else {
					$titles = array();
					$titles[0] = 'data';
				}
				*/
				$tt = 1;

				$c .= '<tr>'. "\n";
				$c .= '<td class="datatable_head_td">-</td>';
				if(is_array($titles)){
				foreach($titles as $key) {

					$c .= '<td class="datatable_head_td"><b>' . $key . '</b></td>';
				}
				}
				$c .='</tr>'. "\n";
			}

			$c .='<tr>'. "\n";
			$c .= '<td class="datatable_head_td"><b>' . $lineKey . '</b></td>';

			if(is_array($line)){
				/*
				foreach($line as $k => $v) {

					$c .='<td class="datatable_td_'. $alt .'"><nobr>' . $v . '</nobr></td>';
				}*/
				foreach($titles as $tkey) {
					$c .='<td class="datatable_td_'. $alt .'"><nobr>' . $line[$tkey] . '</nobr></td>';
				}


			}	else {
				$c .= '<td class="datatable_td_'. $alt .'"><b>' . $line . '</b></td>';
			}
			$c .='</tr>'. "\n";

			$alt = 1 - $alt;
			//print_r2($line);
		}
		$c .='<table>';

		return $c;
	}


	#------------------------------------------------------------------------------


	function debug_table($trace, $file, $line) {
		
		print '[From: <b>'. $file.'</b>, ..... line:<b>'. $line .'</b>]<pre>';
		foreach( $trace as $k => $r) {
		
			print '['. $k .']' . "\n";
			print '[file]: ' . @$r['file'] . "\n";
			print '[line]: ' . @$r['line'] . "\n";
			print '[func]: ' . @$r['function'] . "\n";
			
			$rr = $r;
			foreach(array('file', 'line', 'function') as $key ) { 
				unset($rr[$key]);
			}
			
			foreach($rr as $ekey => $el) {
				print '['. $ekey .']: ';
				
				if( empty($el) ) {
					print '-'. "\n";
					
				} elseif(is_array($el) ) {
					print 'array: [';
					
					$bl = 0;
					foreach($el as $arrkey => $arrval) {
						if( !is_numeric($arrkey)) { print( $arrkey . ': ' ); }
						if($bl == 0) {$bl = 1;} else { print ', '; }
						if(is_array($arrval)) {
							print( '*array*' );
						} elseif(is_object($arrval)) {
							print( '*object* '. get_class($arrval) );
						} else {
							//print( '('. gettype( $arrval) .')'. $arrval ); 
							print( $arrval ); 
						}
						
					}
					
					print ']' . "\n";
					
				} elseif( is_object($el) ) {
					$rand_id = rand(1,99999);
					print 'object:'. get_class($el); print '[<a href="#" onClick="document.getElementById(\'n'.$rand_id.'\').style.display=\'\';return false;">...</a>]';
					print '<div id="n'.$rand_id.'" style="display: none">'.showobj($el).'</div>';
					print "\n";
				} else {
					print $el . "\n";
				}
			}

			print "\n";
		}
		print '</pre>';
	}
	
	function showobj($obj) {
		
		$c = '';
		
		$reflection = new ReflectionClass(get_class($obj));
		foreach ($reflection->getProperties() as $key => $value) {
			$c .= '- ' . $key . ': ' . $value; // . "\n";
		}
		
		return $c;
	}
	
	function showobj2($obj) {
		return '<pre>' . showobj($obj) . '</pre>';
	}
		
	function closure_dump(Closure $c) {
		$str = 'function (';
		$r = new ReflectionFunction($c);
		$params = array();
		foreach($r->getParameters() as $p) {
			$s = '';
			if($p->isArray()) {
				$s .= 'array ';
			} else if($p->getClass()) {
				$s .= $p->getClass()->name . ' ';
			}
			if($p->isPassedByReference()){
				$s .= '&';
			}
			$s .= '$' . $p->name;
			if($p->isOptional()) {
				$s .= ' = ' . var_export($p->getDefaultValue(), TRUE);
			}
			$params []= $s;
		}
		$str .= implode(', ', $params);
		$str .= '){' . PHP_EOL;
		$lines = file($r->getFileName());
		for($l = $r->getStartLine(); $l < $r->getEndLine(); $l++) {
			$str .= $lines[$l];
		}
		return $str;
	}

	#------------------------------------------------------------------------------

?>
