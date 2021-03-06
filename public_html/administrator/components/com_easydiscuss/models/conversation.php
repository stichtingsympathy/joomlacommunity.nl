<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelConversation extends EasyDiscussAdminModel
{
	/**
	 * Category total
	 *
	 * @var integer
	 */
	public $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	public $_pagination = null;

	/**
	 * Category data array
	 *
	 * @var array
	 */
	public $_data = null;

	public function __construct()
	{
		parent::__construct();

		$limit = ($this->app->getCfg('list_limit') == 0) ? 5 : ED::getListLimit();
		$limitstart = $this->input->get('limitstart', '0', 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}


	/**
	 * Method to get a pagination object for the posts
	 *
	 * @access public
	 * @return integer
	 */
	public function getPagination()
	{
		return $this->_pagination;
	}

	/**
	 * Adds a list of recipients that can see a particular message
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int		The unique message id.
	 * @param	int 	The unique recipient id.
	 * @param	int		The unique creator id.
	 */
	public function addMessageMap($conversationId, $messageId, $recipientId, $creator)
	{
		$db = ED::db();

		// Add a record for recipient
		$map = ED::table('ConversationMap');
		$map->user_id = $recipientId;
		$map->conversation_id = $conversationId;
		$map->message_id = $messageId;
		$map->isRead = DISCUSS_CONVERSATION_UNREAD;
		$map->state = DISCUSS_CONVERSATION_PUBLISHED;
		$map->store();

		// Add a record for the creator.
		$map = ED::table('ConversationMap');
		$map->user_id = $creator;
		$map->conversation_id = $conversationId;
		$map->message_id = $messageId;
		$map->isread = DISCUSS_CONVERSATION_READ;
		$map->state = DISCUSS_CONVERSATION_PUBLISHED;
		$map->store();

		return true;

	}

	/**
	 * Adds a participant into a conversation
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The conversation id.
	 * @param	int 	The unique id of the user
	 */
	public function addParticipant($conversationId, $participantId, $creatorId)
	{
		$db = ED::db();
		
		// Add recipient.
		$participant 	= ED::table('ConversationParticipant');
		$participant->conversation_id = $conversationId;
		$participant->user_id = $participantId;
		$participant->store();

		// Add creator.
		$participant = ED::table('ConversationParticipant');
		$participant->conversation_id = $conversationId;
		$participant->user_id = $creatorId;
		$participant->store();

		return true;
	}

	/**
	 * Determines if the conversation is new for the particular node.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int 	The unique user id.
	 * @return	boolean
	 */
	public function isNew($conversationId, $userId)
	{
		$db	= ED::db();

		$query 	= 'SELECT COUNT(1) '
				. 'FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
				. 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'AND c.' . $db->nameQuote( 'isread' ) . '=' . $db->Quote( 0 ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND c.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId ) . ' '
				. 'GROUP BY a.' . $db->nameQuote( 'id' );
		$db->setQuery($query);

		$isNew = $db->loadResult() > 0;

		return $isNew;
	}

	/**
	 * Toggle a conversation read state.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int		The unique user id.
	 * @param	int 	The read state
	 */
	public function toggleRead( $conversationId , $userId , $state )
	{
		$db = ED::db();
		$query = 'UPDATE ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' '
				. 'SET ' . $db->nameQuote( 'isread' ) . ' = ' . $db->Quote( $state ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}
	/**
	 * Mark a conversation to old.
	 *
	 * @since	3.0
	 * @access	public
	 * @return	boolean
	 * @param	int $conversationId
	 * @param	int $userId
	 */
	public function markAsRead( $conversationId , $userId )
	{
		return $this->toggleRead( $conversationId , $userId , DISCUSS_CONVERSATION_READ );
	}

	/**
	 * Mark a conversation to new.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int $conversationId
	 * @param	int $userId
	 *
	 * @return	boolean
	 */
	public function markAsUnread($conversationId, $userId)
	{
		return $this->toggleRead($conversationId, $userId, DISCUSS_CONVERSATION_UNREAD);
	}

	/**
	 * Toggling archiving a conversation. Simply modifying the state.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function toggleArchive($conversationId, $userId, $state)
	{
		$db = ED::db();
		$query = array();

		$query[] = 'UPDATE `#__discuss_conversations_message_maps` SET `state` = ' . $db->Quote($state);
		$query[] = 'WHERE `conversation_id` = ' . $db->Quote($conversationId);
		$query[] = 'AND `user_id` = ' . $db->Quote($userId);

		$db->setQuery(implode(' ', $query));
		$db->Query();

		return true;
	}

	/**
	 * Archiving a conversation.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function archive($conversationId, $userId)
	{
		return $this->toggleArchive($conversationId, $userId, DISCUSS_CONVERSATION_ARCHIVED);
	}
	
	/**
	 * Unarchiving a conversation.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function unArchive($conversationId, $userId)
	{
		return $this->toggleArchive($conversationId, $userId, DISCUSS_CONVERSATION_PUBLISHED);
	}

	/**
	 * Remove the child message mapping for the particular node.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique conversation id.
	 * @param	int 	The unique user id which owns the message mapping.
	 * @return	boolean
	 */
	public function deleteMapping($conversationId, $userId)
	{
		$db = ED::db();
		$query = array();

		// Delete the conversation items
		$query[] = 'DELETE FROM ' . $db->nameQuote('#__discuss_conversations_message_maps');
		$query[] = 'WHERE ' . $db->nameQuote('conversation_id') . ' = ' . $db->Quote($conversationId);
		$query[] = 'AND ' . $db->nameQuote('user_id') . '=' . $db->Quote($userId);
		$query = implode(' ' , $query);

		$db->setQuery($query);
		$db->Query();

		// @rule: Check if this is the last child item. If it is the last, we should delete everything else.
		$query	= 'SELECT COUNT(DISTINCT( c.' . $db->nameQuote( 'user_id' ) . ')) '
				. 'FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
				. 'INNER JOIN '. $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
				. 'ON b.' . $db->nameQuote( 'id' ) . ' = c.' . $db->nameQuote( 'message_id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND c.' . $db->nameQuote( 'user_id' ) . ' != ' . $db->Quote( $userId ) . ' '
				. 'GROUP BY a.' . $db->nameQuote( 'id' );
		$db->setQuery($query);
		$total = $db->loadResult();

		if ($total <= 0) {		
			return $this->cleanup($conversationId);
		}
	}

	/**
	 * Completely removes the conversation from the site.
	 *
	 * @return	boolean
	 * @param	int $conversationId
	 */
	private function cleanup($conversationId)
	{
		$db		= ED::db();

		// @rule: Delete conversation first
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		// @rule: Delete messages for the conversation.
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		// @rule: Delete messages mapping for the conversation.
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		// @rule: Delete participants for the conversation.
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__discuss_conversations_participants' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId );
		$db->setQuery( $query );
		$db->Query();

		return true;
	}

	/**
	 * Checks whether or not the node id has any access to the conversation.
	 *
	 * @return	boolean
	 * @param	int $conversationId
	 * @param	int $userId
	 */
	public function hasAccess( $conversationId , $userId )
	{
		$db		= ED::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__discuss_conversations_participants' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND ' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );
		$db->setQuery( $query );

		return ( $db->loadResult() > 0 );
	}

	/**
	 * Retrieves a list of users who are participating in a conversation.
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		$conversationId		The unique id of that conversation
	 * @param	array	$excludeUsers		Exlude a list of nodes
	 *
	 * @return	array	An array that contains SocialUser objects.
	 */
	public function getParticipants($conversationId, $currentUserId = null)
	{
		$db = $this->db;
		$query	= 'SELECT DISTINCT(a.' . $db->nameQuote('user_id') . ') FROM ' . $db->nameQuote('#__discuss_conversations_participants') . ' AS a '
				. 'WHERE a.' . $db->nameQuote('conversation_id') . ' = ' . $db->Quote($conversationId);

		if (!is_null($currentUserId)) {
			$query 	.= ' AND a.' . $db->nameQuote('user_id') . '!=' . $db->Quote($currentUserId);
		}

		$db->setQuery($query);
		$participants = $db->loadResultArray();

		return $participants;
	}

	/**
	 * Retrieves the last message from a conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getLastReplier($conversationId, $userId)
	{
		$db = $this->db;
		$query	= 'SELECT b.`created_by` FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
				. 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote($conversationId) . ' '
				. 'AND c.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote($userId) . ' '
				. 'ORDER BY b.' . $db->nameQuote( 'created' ) . ' DESC '
				. 'LIMIT 1';

		$db->setQuery($query);

		$result = $db->loadResult();

		$user = ED::user($result);
		
		return $user;
	}

	/**
	 * Retrieves the last message from a conversation
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return	
	 */
	public function getLastMessage($conversationId, $userId)
	{
		$db = $this->db;
		$query	= 'SELECT b.`message` FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b '
				. 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' ) . ' '
				. 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c '
				. 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'id' ) . ' = ' . $db->Quote($conversationId) . ' '
				. 'AND c.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote($userId) . ' '
				. 'ORDER BY b.' . $db->nameQuote( 'created' ) . ' DESC '
				. 'LIMIT 1';
		$db->setQuery($query);

		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Retrieves a list of messages in a particular conversation
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The unique id of that conversation
	 * @param	int		The current user id of the viewer.
	 *
	 */
	public function getMessages($conversationId, $userId, $viewAll = false, $count = false, $userMessageOnly = false)
	{
		$db = $this->db;
		$config = ED::config();

		$operation  = '( UNIX_TIMESTAMP( \'' . ED::date()->toSql() . '\' ) - UNIX_TIMESTAMP( a.`created`) )';

		$query	= 'SELECT a.* ';
		$query  .= ', FLOOR( ' . $operation. ' / 60 / 60 / 24) AS daydiff '
				. 'FROM ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS a '
				. 'LEFT JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS b '
				. 'ON b.' . $db->nameQuote( 'message_id' ) . ' = a.' . $db->nameQuote( 'id' ) . ' '
				. 'WHERE a.' . $db->nameQuote( 'conversation_id' ) . ' = ' . $db->Quote( $conversationId ) . ' '
				. 'AND b.' . $db->nameQuote( 'user_id' ) . ' = ' . $db->Quote( $userId );

		if ($userMessageOnly) {
			$query .= ' AND a.' . $db->nameQuote('created_by') . ' = ' . $db->Quote($userId);
		}

		// @rule: Messages ordering.
		// @TODO: respect ordering settings.
		$query	.= 'ORDER BY a.' . $db->nameQuote( 'created' ) . ' ASC';

		// By default show the latest messages limit by the numbers specified in backend
		if (!$viewAll) {
			$query 	.= ' LIMIT ' . $config->get('main_messages_limit', 5);
		}

		// If view == 'all', do nothing because we wanted to show all messages.

		if ($viewAll === 'previous') {
			$count = $config->get('main_messages_limit', 5) + $count;
			
			// View another 5 more previous messages
			$query 	.= ' LIMIT ' . $count;
		}

		$db->setQuery($query);

		$rows = $db->loadObjectList();
		$messages = array();

		foreach ($rows as $row) {
			$message = ED::table( 'ConversationMessage' );
			$message->bind($row);
			$message->daydiff = $row->daydiff;
			$messages[]	= $message;
		}
		return $messages;
	}

	/**
	 * Retrieves a total number of conversations for a particular user
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The current user id of the viewer
	 */
	public function getCount( $userId , $options = array() )
	{
		$db			= ED::db();

		$query		= array();
		$query[]	= 'SELECT COUNT(1) FROM(';
		$query[]	= 'SELECT a.' . $db->nameQuote( 'id' ) . ' FROM ' . $db->nameQuote( '#__discuss_conversations' ) . ' AS a';
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message' ) . ' AS b';
		$query[]	= 'ON a.' . $db->nameQuote( 'id' ) . ' = b.' . $db->nameQuote( 'conversation_id' );
		$query[]	= 'INNER JOIN ' . $db->nameQuote( '#__discuss_conversations_message_maps' ) . ' AS c';
		$query[]	= 'ON c.' . $db->nameQuote( 'message_id' ) . ' = b.' . $db->nameQuote( 'id' );
		$query[]	= 'WHERE c.' . $db->nameQuote( 'user_id' ) . '=' . $db->Quote( $userId );

		// @rule: Respect filter options
		if( isset( $options['filter'] ) )
		{
			switch( $options[ 'filter' ] )
			{
				case 'unread':
					$query[]	= 'AND c.' . $db->nameQuote( 'isread' ) . '=' . $db->Quote( DISCUSS_CONVERSATION_UNREAD );
				break;
			}
		}

		// @rule: Process any additional filters here.
		if( isset( $options[ 'archives' ] ) && $options[ 'archives' ] )
		{
			$query[]	= ' AND c.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( DISCUSS_CONVERSATION_ARCHIVED );
		}
		else
		{
			$query[]	= ' AND c.' . $db->nameQuote( 'state' ) . ' = ' . $db->Quote( DISCUSS_CONVERSATION_PUBLISHED );
		}

		$query[]	= 'GROUP BY a.' . $db->nameQuote( 'id' );

		$query[]	= ') AS x';

		// Join back query with a proper glue.
		$query		= implode( ' ' , $query );

		$db->setQuery( $query );
		$total 		= $db->loadResult();

		return $total;
	}

	/**
	 * Retrieves a list of conversations for a particular node
	 *
	 * @since	3.0
	 * @access	public
	 * @param	int		The current user id of the viewer
	 */
	public function getConversations($userId , $options = array())
	{
		$db = ED::db();

		$query = array();
		$query[] = 'SELECT a.*, b.`message`, c.`isread`';
		$query[] = 'FROM `#__discuss_conversations` as `a`';
		$query[] = 'INNER JOIN `#__discuss_conversations_message` as `b` ON a.`id` = b.`conversation_id`';
		$query[] = 'INNER JOIN `#__discuss_conversations_message_maps` as `c` on c.`message_id` = b.`id`';
		$query[] = 'WHERE c.`user_id` = ' . $db->Quote($userId);

		$archives = ED::normalize($options, 'archives', false);

		if ($archives) {
			$query[] = 'AND c.`state` = ' . $db->Quote(DISCUSS_CONVERSATION_ARCHIVED);
		} else {
			$query[] = 'AND c.`state` = ' . $db->Quote(DISCUSS_CONVERSATION_PUBLISHED);
		}

		$filter = ED::normalize($options, 'filter', false);

		if ($filter == 'unread') {
			$query[] = 'AND c.`isread` = ' . $db->Quote(DISCUSS_CONVERSATION_UNREAD);
			$query[] = 'AND b.`created_by` != ' . $db->Quote($userId);
		}

		$query[] = 'GROUP BY b.`conversation_id`';

		$sorting = ED::normalize($options, 'sorting', 'latest');

		if ($sorting == 'latest') {
			$query[] = 'ORDER BY a.`lastreplied` DESC';
		}

		$limit = ED::normalize($options, 'limit', false);

		if ($limit) {
			$query[] = 'LIMIT 0, ' . $limit;
		}

		$query = implode(' ', $query);
		// dump(str_replace('#_', 'jos', $query));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if (!$rows) {
			return $rows;
		}

		$conversations = array();

		foreach ($rows as $row) {
			$conversation = ED::conversation($row);
			$conversations[] = $conversation;
		}

		return $conversations;
	}

	/**
	 * Inserts a new reply into an existing conversation.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	int		$conversationId		The conversation id.
	 * @param	string	$message			The content of the reply.
	 * @param 	int 	$creatorId			The user's id.
	 *
	 * @return	SocialTableConversationMessage	The message object
	 */
	public function insertReply($conversationId, $content, $creatorId)
	{
		$conversation = ED::table('Conversation');
		$conversation->load($conversationId);

		// Store the new message first.
		$message = ED::table('ConversationMessage');
		$message->conversation_id = $conversationId;
		$message->message = $content;
		$message->created_by = $creatorId;
		$message->created = ED::date()->toSql();
		$message->store();

		// Since a new message is added, add the visibility of this new message to the participants.
		$users = $this->getParticipants($conversation->id);

		foreach ($users as $userId) {
			$map = ED::table('ConversationMap');
			$map->user_id = $userId;
			$map->conversation_id = $conversation->id;
			$map->state = DISCUSS_CONVERSATION_PUBLISHED;
			$map->isread = $userId == $creatorId ? DISCUSS_CONVERSATION_READ : DISCUSS_CONVERSATION_UNREAD;
			$map->message_id = $message->id;
			$map->store();
		}

		// Update the last replied date.
		$conversation->lastreplied = ED::date()->toSql();
		$conversation->store();

		return $message;
	}

	/**
	 * Responsible to format message replies.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	Array 	An array of result.
	 */
	public static function formatConversationReplies(&$replies)
	{
		if (!$replies) {
			return false;
		}

		foreach ($replies as &$reply) {
			$reply->creator = ED::profile($reply->created_by);
			$reply->message	= ED::parseContent($reply->message);
			$reply->lapsed = ED::date()->getLapsedTime($reply->created);
		}
	}

	/**
	 * Responsible to format message items.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	Array 	An array of result.
	 */
	public static function formatConversations(&$conversations)
	{
		if (!$conversations) {
			return false;
		}

		$my = JFactory::getUser();

		foreach ($conversations as &$conversation) {
			// Get the participant.

			$model = ED::model('conversation');
			$participants = $model->getParticipants($conversation->id, $my->id);

			$creator = ED::profile($participants[0]);

			$conversation->creator = $creator;

			$intro = $conversation->getLastMessage($my->id);

			// @TODO: Configurable length.
			$length = EDJString::strlen($intro);
			$intro = EDJString::substr(strip_tags($intro), 0, 10);

			// Append ellipses if necessary.
			if ($length > 10) {
				$intro .= JText::_('COM_EASYDISCUSS_ELLIPSES');
			}

			$conversation->intro = $intro;
			$conversation->lapsed = ED::date()->getLapsedTime($conversation->lastreplied);
		}
	}

	/**
	 * Method to get conversation data for GDPR purpose
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getConversationGDPR($options)
	{
		$db = $this->db;

		$userId = isset($options['userId']) ? $options['userId'] : false;
		$limit = isset($options['limit']) ? $options['limit'] : false;
		$exclude = isset($options['exclude']) ? $options['exclude'] : false;

		$query = 'SELECT a.*,b.' . $db->nameQuote('message') . ',c.' . $db->nameQuote('isread');
		$query .= ' FROM ' . $db->nameQuote('#__discuss_conversations') . ' AS a ';
		$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_conversations_message') . ' AS b ';
		$query .= ' ON a.' . $db->nameQuote('id') . ' = b.' . $db->nameQuote('conversation_id');
		$query .= ' INNER JOIN ' . $db->nameQuote('#__discuss_conversations_message_maps') . ' AS c ';
		$query .= ' ON c.' . $db->nameQuote('message_id') . ' = b.' . $db->nameQuote('id');

		$query .= ' WHERE c.' . $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);
		$query .= ' AND b.' . $db->nameQuote('created_by') . ' = ' . $db->Quote($userId);

		if ($exclude) {
			$query .= ' AND a.`id` NOT IN(' . implode(',', $exclude) . ')';
		}

		$query .= ' GROUP BY b.' . $db->nameQuote('conversation_id');
		$query .= ' ORDER BY a.' . $db->nameQuote('lastreplied') . ' DESC';
		$query .= ' LIMIT 0,' . $limit;

		$db->setQuery($query);

		$result = $db->loadObjectList();
		$conversations = array();

		if ($result) {
			foreach ($result as $conversation) {
				$conversation = ED::conversation($conversation);
				$conversations[] = $conversation;
			}
		}

		return $conversations;
	}
}
