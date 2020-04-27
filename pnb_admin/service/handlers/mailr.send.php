<?php

ini_set("memory_limit","512M");

function mail_utf8($to, $subject = '(No subject)', $message = '', $header = '') {
  $header_ = 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n";
  mail($to, '=?UTF-8?B?'.base64_encode($subject).'?=', $message, $header_ . $header);
}

function prepare_description($m, $mfields, $inst, $ifields, $mfieldgrps) {

  $desc = array('email' => '', 'name' => '', 'data' => array() );
  $desc['email'] = $m['fields'][20];
  $desc['name'] = $m['fields'][1].' '.$m['fields'][3];

  if ( empty($m['fields'][10]) && empty($m['fields'][11]) && empty($m['fields'][12]) ) {
	  $m['fields'][10] = $inst[ $m['fields'][17] ]['fields'][10];
	  $m['fields'][11] = $inst[ $m['fields'][17] ]['fields'][11];
  }

  if ( empty($m['fields'][4]) )  { $m['fields'][4]  = $m['fields'][3]; }
  if ( empty($m['fields'][13]) ) { $m['fields'][13] = $inst[ $m['fields'][17] ]['fields'][12]; }
  if ( empty($m['fields'][14]) ) { $m['fields'][14] = $inst[ $m['fields'][17] ]['fields'][13]; }
  if ( empty($m['fields'][15]) ) { $m['fields'][15] = $inst[ $m['fields'][17] ]['fields'][14]; }
  if ( empty($m['fields'][16]) ) { $m['fields'][16] = $inst[ $m['fields'][17] ]['fields'][15]; }
  if ( empty($m['fields'][25]) ) { $m['fields'][25] = ( $inst[ $m['fields'][17] ]['fields'][8] ) ? ( $inst[ $m['fields'][17] ]['fields'][8] ) : ( $inst[ $m['fields'][17] ]['fields'][7] ); }

//  foreach( $m['fields'] as $k => $v ) {
//	if ( $k == 84 || $k == 85 || $k == 86 || $k == 5 
//	  || $k == 40 || $k == 41 || $k == 42 || $k == 43 || $k == 44 || $k == 45 || $k == 46 ) { continue; }
  foreach( $mfields as $k => $v ) {
	if ( $k == 17 ) {
	  $desc['data'][] = array( 0 => 'Home institution', 1 => $inst[ $m['fields'][17] ]['fields'][1] );
	} else {
	  $desc['data'][] = array( 0 => $mfields[$k]['name_desc'], 1 => ( !empty( $m['fields'][$k] ) ? $m['fields'][$k] : 'N/A' ) );
	}
  }

  return $desc;
}

function send_mail_to_representative($r, $ifields, $mfields) {
    $to = trim($r['rep']['email']);
    $from = 'morrison@bnl.gov';
    $subject = 'sPHENIX PhoneBook information - please review';

/*
    $message = 'Dear '.$r['rep']['first_name'].' '.$r['rep']['last_name'].",\n\n"
		.'You are receiving this Email as you are the Institutional Board member for '
		.'the sPHENIX '.$r['inst'][2].' group. This is an automatically generated Email showing the '
		.'information we have for your group in the sPHENIX PhoneBook, and we expect to receive your corrections within two weeks from today. '
		.'Please do not remove or delete the original information but '."\n"
		.'- correct the fields that are incorrect by adding the proper information after the arrow "->"'."\n"
		.'- reply to this Email with the corrected information, if you have no corrections '
		.'please reply and indicate that all is correct. '."\n\n";
*/

    $message = 'Dear '.$r['rep']['first_name'].' '.$r['rep']['last_name'].",\n\n"
    .' As the Institutional Board representative of '.$r['inst'][1].', we\'d '
    .'appreciate it if you could take a few minutes to look over (and possibly '
    .'update) the information we have regarding sPHENIX collaborators from '
    .'your institution.  The information below is taken straight from the '
    .'collaboration database.  Please add the names and contact information '
    .'for anyone who has joined the sPHENIX effort, let us know of anyone who '
    .'should no longer be on this list, and make sure the information about '
    .'your institution is correct. '."\n\n"
    .'We\'d appreciate it if you could attend to this by November 18, 2016.'."\n\n"
    .'Once we\'ve compiled an updated list of collaborators, we plan to send '
    .'out a short survey to every individual member of the collaboration to '
    .'find out about their technical and physics interests, as well as to '
    .'solicit their input and advice.  We expect to repeat this annually to '
    .'make sure we always have an up to date picture of the collaboration.'."\n\n"
    .'Regards,'."\n"
    .'Dave and Gunther'."\n"
    .'sPHENIX co-spokespersons'."\n\n";

	// member information
	if (!empty($r['members'])) {
		$message .= '********* Group Member Info *********'."\n".' Please review the list below carefully.'."\n\n";

		usort($r['members'], 'mem_cmp');
		foreach($r['members'] as $k => $v) {
			$message .= $v[3].', '.$v[1].' -> '."\n";
		}
		$message .= "\n\n";
		$message .= 'If new members have appeared in your group, please send the'
			.' following information: First Name, Last Name, Gender, Email address, Office phone number.'."\n\n";
	}

	// institution information
	$message .=	'********* Institution Information *********'."\n";
	$inst = $r['inst'];
	$flds = array( 0 => 1, 1 => 2, 2 => 5, 3 => 4, 4 => 7, 5 => 8, 6 => 10, 7 => 11, 8 => 12, 9 => 13, 10 => 14,
		11 => 15, 12 => 16, 13 => 43, 14 => 44 );

	foreach($flds as $k => $v) {
	  $message .= $ifields[$v]['name_desc'].' = '.$inst[$v]." -> \n";
	  if (!empty($ifields[$v]['hint_full']) ) {
	  	$message .='('.$ifields[$v]['hint_full'].')'."\n";
	  }
	  $message .= "\n";
	}


	// associated institutions information
	if (!empty($r['associated_institutions'])) {
		$message .= '******** Associated Institutions Info ********'."\n\n"
		.'Below are records for institutes associated to the '.$r['inst'][2].' institution.'
		.' You are equally responsible to communicate and update those records.'."\n\n";

		foreach($r['associated_institutions'] as $k => $v) {
			$message .= 'o '.$v['inst'][1]."\n";
		}
		$message .= "\n";

		foreach($r['associated_institutions'] as $k => $v) {
			// assoc member details
			usort($v['members'], 'mem_cmp');
			foreach($v['members'] as $k2 => $v2) {
				$message .= $v2[3].', '.$v2[1].', is author = '.$v2[40].' -> '."\n";
			}
			$message .= "\n";
			$message .= "\n";

			// assoc institution details
			$message .= '*** '.$v['inst'][1].' ***'."\n\n";
			foreach($flds as $k2 => $v2) {
			  $message .= $ifields[$v2]['name_desc'].' = '.$v['inst'][$v2]." -> \n";
			  if ( !empty($ifields[$v2]['hint_full']) ) {
			  	$message .='('.$ifields[$v2]['hint_full'].')'."\n";
			  }
			  $message .= "\n";
			}
		}
	}

	// the end
	$message .= '*****************************************'."\n\n"
    .'We appreciate your efforts to keep our records accurate.'."\n"
	.'sPHENIX automated phonebook system';
    if (isset($from) and strlen($from)) {
        $additional  = 'From: ' . $from."\r\n";
;	//$additional .= 'Bcc: morrison@bnl.gov' . "\r\n";
    }

	// FIXME: remove when done testing
	$to = 'arkhipkin@bnl.gov';

	mail_utf8($to,$subject,$message,$additional);

	return array('success' => true);
}

function mailr_send_handler($params) {

  $mem = file_get_contents('https://www.sphenix.bnl.gov/pnb/service/?q=/members/list/status:active/details:full&rnd='.time(0));
  $mem = json_decode($mem, true);

  $mfields = file_get_contents('https://www.sphenix.bnl.gov/pnb/service/?q=/service/list/object:fields/type:members&rnd='.time(0));
  $mfields = json_decode($mfields, true);

  unset($mfields[84]); unset($mfields[85]); unset($mfields[86]); unset($mfields[87]);
  unset($mfields[40]); unset($mfields[41]); unset($mfields[42]); unset($mfields[43]);
  unset($mfields[44]); unset($mfields[45]); unset($mfields[46]);

  $mfieldgrps = file_get_contents('https://www.sphenix.bnl.gov/pnb/service/?q=/service/list/object:fieldgroups/type:members&rnd='.time(0));
  $mfieldgrps = json_decode($mfieldgrps, true);

  $inst = file_get_contents('https://www.sphenix.bnl.gov/pnb/service/?q=/institutions/list/status:active/details:full&rnd='.time(0));
  $inst = json_decode($inst, true);

  $ifields = file_get_contents('https://www.sphenix.bnl.gov/pnb/service/?q=/service/list/object:fields/type:institutions&rnd='.time(0));
  $ifields = json_decode($ifields, true);

  // select active members who did not leave yet
  $memlist = array();
  foreach( $mem as $k => $v ) {
    $inst_id = ( $inst[ $mem[$k]['fields'][17] ]['fields'][45] ? $inst[ $mem[$k]['fields'][17] ]['fields'][45] : $mem[$k]['fields'][17] );
	if ( empty($v['fields'][17]) ) {
		//echo 'no institution assigned'."\n";
		unset($mem[$k]);
		continue;
	} 
	if ( empty($inst[$inst_id]) ) {
		//echo 'no institution exists'."\n";
		unset($mem[$k]);
		continue;
	}
	if ( !empty($inst[$inst_id]['fields'][42]) && ($inst[$inst_id]['fields'][42] != '0000-00-00 00:00:00') ) {
		//echo 'left star: '.print_r($inst[$inst_id]['fields'][42], true)."\n";
		unset($mem[$k]);
		continue;
	}
	if ( !empty( $v['fields'][85] )
		  && $v['fields'][85] != '0000-00-00 00:00:00'
      	  && (
			  ( time(0) - strtotime($v['fields'][85]) ) > 1
			)
		)
	{
		//echo 'left star over 6 months ago'."\n";
        unset($mem[$k]);
		continue;
    }
  }

  // filter institutions, remove ones without representative
  foreach($inst as $k => $v) {
	if ( !empty($v['fields'][45]) ) { continue; } // parent exists, representative is not needed
	if ( !empty($v['fields'][42]) && $v['fields'][42] != '0000-00-00 00:00:00' ) {
		unset($inst[$k]);
		continue;
	}
	if ( empty( $v['fields'][9] ) ) { 
		echo 'no IB representative!'."\n";
		print_r($inst[$k]);
		unset($inst[$k]);
		continue;
	}
	if ( empty( $mem[$v['fields'][9]] ) ) {
		echo 'IB representative left sPHENIX, id: '.$v['fields'][9]."\n";
		print_r($inst[$k]);
		unset($inst[$k]);
		continue;
	}
  }

  $rep = array(); // by inst id
  foreach($inst as $k => $v) {
	if ( !empty($v['fields'][45]) ) {
		if ( empty($rep[ $v['fields'][45] ]) ) {
			echo 'adding dep array'."\n";
			$rep[$v['fields'][45]] = array();
			$rep[$v['fields'][45]]['associated_institutions'] = array();
		}
		$rep[$v['fields'][45]]['associated_institutions'][$k] = array(
			'inst' => $v['fields'],
			'members' => array()
		);
	} else {
		$rep[$k] = array();
		$tmp = $mem[ $v['fields'][9] ]['fields'];
		$rep[$k]['rep'] = array( 'first_name' => $tmp[1], 'last_name' => $tmp[3], 'email' => $tmp[20] );
		$rep[$k]['inst'] = $v['fields'];
		$rep[$k]['members'] = array();
		$rep[$k]['associated_institutions'] = array();
	}
  }

  foreach($mem as $k => $v) {
	$orig_id = intval($v['fields'][17]);
    $inst_id = ( $inst[ $v['fields'][17] ]['fields'][45] ? $inst[ $v['fields'][17] ]['fields'][45] : $v['fields'][17] );
	if ( $inst_id != $orig_id ) { // associated member
		$rep[$inst_id]['associated_institutions'][$orig_id]['members'][$k] = $v['fields'];
	} else {
		$rep[$inst_id]['members'][$k] = $v['fields'];
	}
  }

  foreach($rep as $k => $v) {
	// FIXME: remove if { } when done
	if ($k == 5) {
		send_mail_to_representative($v, $ifields, $mfields);
	}
  }

  return json_encode(array('success' => true));
  exit;
}

function mem_cmp($a, $b)
{
    return strcmp($a[3], $b[3]);
}