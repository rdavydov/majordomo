<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  //searching 'TITLE' (varchar)

  if (preg_match('/(\d+)\.html/', $_SERVER["REQUEST_URI"], $m)) {
   $qry.=" AND scenes.ID='".$m[1]."'";
  } elseif (!$out['CONTROLPANEL']) {
   $qry.=" AND scenes.HIDDEN!=1";
  }

  global $title;
  if ($title!='') {
   $qry.=" AND TITLE LIKE '%".DBSafe($title)."%'";
   $out['TITLE']=$title;
  }
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['scenes_qry'];
  } else {
   $session->data['scenes_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_scenes;
  if (!$sortby_scenes) {
   $sortby_scenes=$session->data['scenes_sort'];
  } else {
   if ($session->data['scenes_sort']==$sortby_scenes) {
    if (Is_Integer(strpos($sortby_scenes, ' DESC'))) {
     $sortby_scenes=str_replace(' DESC', '', $sortby_scenes);
    } else {
     $sortby_scenes=$sortby_scenes." DESC";
    }
   }
   $session->data['scenes_sort']=$sortby_scenes;
  }
  if (!$sortby_scenes) $sortby_scenes="PRIORITY DESC, TITLE";
  $out['SORTBY']=$sortby_scenes;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM scenes WHERE $qry ORDER BY ".$sortby_scenes);
  if ($this->action!='admin') {
   $total=count($res);
   $res2=array();
   for($i=0;$i<$total;$i++) {
    if (checkAccess('scene', $res[$i]['ID'])) {
     $res2[]=$res[$i];
    }
   }
   $res=$res2;
   unset($res2);
  }

  if ($res[0]['ID']) {
   $total=count($res);
   $positions=array();
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
      $res[$i]['ELEMENTS']=$this->getElements("SCENE_ID='".$res[$i]['ID']."' AND CONTAINER_ID=0");
      $res[$i]['NUM']=$i;
      $res[$i]['NUMP']=$i+1;
   }
   if ($total==1) {
    $out['SCENE_ID']=$res[0]['ID'];
   }
   $out['TOTAL']=$total;
   $out['RESULT']=$res;
  }

?>