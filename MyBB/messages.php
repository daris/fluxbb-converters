<?php // this file is not updated for new pms

// Check if New PMS mod is installed
if ($start == 0 && !$db->table_exists('pms_new_posts'))
	next_step();

$result = $fdb->query('SELECT m.*,t.*,u.username FROM '.$fdb->prefix.$_SESSION['phpnuke'].'privmsgs AS m, '.$fdb->prefix.$_SESSION['phpnuke'].'privmsgs_text AS t, '.$fdb->prefix.'users AS u WHERE t.privmsgs_text_id=privmsgs_id AND m.privmsgs_from_userid=u.user_id AND m.privmsgs_id>'.$start.' ORDER BY m.privmsgs_id LIMIT '.$_SESSION['limit']) or myerror("Unable to get message list", __FILE__, __LINE__, $fdb->error());
$last_id = -1;
while($ob = $fdb->fetch_assoc($result))
{
	$last_id = $ob['privmsgs_id'];
	echo htmlspecialchars($ob['username']).' ('.$ob['privmsgs_id'].")<br>\n"; flush();

	// Check for anonymous poster id problem
	if($ob['privmsgs_from_userid'] == -1){
		$ob['privmsgs_from_userid'] = 1;
		$ob['username'] = 'Guest';
	}
/*
Skickar meddelande:
	1: Skickat, användaren inte läst det [ Outbox ]
	2: Meddelandet kopieras från orginalmeddelandet [ Sentbox ]
	4: Save message [ Savebox ]

Mottagit meddelande:
	5: Sett att det finns, inte läst det [ Inbox ]
	0: Läst meddelandet [ Inbox ]
	3: Save message [ Savebox ]
*/
	switch($ob['privmsgs_type']){

		// Send message:
		
		// 1: Message sent, receiver hasn't read it [ Outbox ]
		case 1:
			// Owners message
			$ob['owner']  = $ob['privmsgs_from_userid'];
			$ob['sender'] = $ob['privmsgs_from_userid'];
			$ob['status'] = 1;
			$ob['showed'] = 1;

			// Receivers message
			$db->query('INSERT INTO '.$db->prefix.'messages
				(owner, subject, message, sender, sender_id, posted, sender_ip, smileys, status, showed) VALUES(
				'.$ob['privmsgs_to_userid'].',
				\''.addslashes($ob['privmsgs_subject']).'\',
				\''.addslashes($ob['privmsgs_text']).'\',
				\''.addslashes($ob['username']).'\',
				'.$ob['privmsgs_from_userid'].',
				'.$ob['privmsgs_date'].',
				\''.decode_ip($ob['privmsgs_ip']).'\',
				'.$ob['privmsgs_enable_smilies'].',
				0,
				0
				)') or myerror("Unable to save post", __FILE__, __LINE__, $db->error());

			break;

		// 2: Message copied from the original one[ Sentbox ]
		// 4: Save message [ Savebox ]
		case 2:
		case 4:
			$ob['owner']  = $ob['privmsgs_from_userid'];
			$ob['sender'] = $ob['privmsgs_from_userid'];
			$ob['status'] = 1;
			$ob['showed'] = 1;
			break;


		// Message received:

		// 5: Seen the message, but haven't read it [ Inbox ]
		case 5:
			// Sender message
			$ob['owner']  = $ob['privmsgs_from_userid'];
			$ob['sender'] = $ob['privmsgs_from_userid'];
			$ob['status'] = 1;
			$ob['showed'] = 1;

			// Receiver message
			$db->query('INSERT INTO '.$db->prefix.'messages
				(owner, subject, message, sender, sender_id, posted, sender_ip, smileys, status, showed) VALUES(
				'.$ob['privmsgs_to_userid'].',
				\''.addslashes($ob['privmsgs_subject']).'\',
				\''.addslashes($ob['privmsgs_text']).'\',
				\''.addslashes($ob['username']).'\',
				'.$ob['privmsgs_from_userid'].',
				'.$ob['privmsgs_date'].',
				\''.decode_ip($ob['privmsgs_ip']).'\',
				'.$ob['privmsgs_enable_smilies'].',
				0,
				0
				)') or myerror("Unable to save post", __FILE__, __LINE__, $db->error());
			
			break;

		// 0: Read the message [ Inbox ]
		// 3: Saved a message [ Savebox ]
		case 0:
		case 3:
			$ob['owner']  = $ob['privmsgs_to_userid'];
			$ob['sender'] = $ob['privmsgs_from_userid'];
			$ob['status'] = 0;
			$ob['showed'] = 1;
			break;

	}

	// Save to database
	$db->query('INSERT INTO '.$db->prefix.'messages
		(owner, subject, message, sender, sender_id, posted, sender_ip, smileys, status, showed) VALUES(
		'.$ob['owner'].',
		\''.addslashes($ob['privmsgs_subject']).'\',
		\''.addslashes($ob['privmsgs_text']).'\',
		\''.addslashes($ob['username']).'\',
		'.$ob['sender'].',
		'.$ob['privmsgs_date'].',
		\''.decode_ip($ob['privmsgs_ip']).'\',
		'.$ob['privmsgs_enable_smilies'].',
		'.$ob['status'].',
		'.$ob['showed'].'
		)') or myerror("Unable to save post", __FILE__, __LINE__, $db->error());
}

convredirect('privmsgs_id', $_SESSION['phpnuke'].'privmsgs', $last_id);
