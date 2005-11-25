<?PHP
/*
 * CalitrixWiki (c) Copyright 2004 by Johannes Klose
 * E-Mail: exe@calitrix.de
 * Project page: http://developer.berlios.de/projects/calitrixwiki
 * 
 * CalitrixWiki is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * CalitrixWiki is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CalitrixWiki; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **/

/**
 * This is the uploads specialpage. It provides a interface
 * to browse uploaded files and upload new files.
 *
 * @author Johannes Klose <exe@calitrix.de>
 **/
class special_uploads extends core
{
	var $uploadTemplate  = 'special_uploads.tpl';
	var $uploadFileTypes = array();
	var $uploadError     = '';
	var $fileConfirm     = false;
	var $fileAction      = '';
	var $fileOrig        = '';
	var $fileUserId      = 0;
	var $fileUserName    = '';
	var $fileDesc        = '';
	var $fileData        = array();
	
	/**
	 * Start function
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param object &$core Core class object
	 * @return void
	 **/
	function start()
	{
		if(!$this->hasPerms(PERM_UPLOAD)) {
			$this->messageEnd('wiki_perm_denied');
		}
		
		$tpl = &singleton('template');
		
		$tpl->assign('isMessage',     false);
		$tpl->assign('isError',       false);
		$tpl->assign('valUploadDesc', '');
		$tpl->assign('valUploadUser', '');
		
		if($this->loggedIn) {
			$tpl->assign('valUploadUser', htmlentities($this->user['user_name']));
		}
		
		$this->initFileTypesArray();
		
		$op = isset($this->get['op']) ? $this->get['op'] : '';
		
		switch($op)
		{
			case 'new':  $this->uploadNew();  break;
			case 'file': $this->uploadView(); break;
			default:     $this->uploadList(); break;
		}
	}
	
	/**
	 * Makes a list of uploads, matching the filter rules.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function uploadList()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$files  = array();
		$result = $db->query('SELECT f.file_id, f.file_orig_name, file_ext, '.
		'file_size, u.user_id, u.user_name, f.file_user_name, f.file_description, '.
		'file_upload_time, file_version FROM '.DB_PREFIX.'uploads f LEFT JOIN '.DB_PREFIX.'users u '.
		'ON u.user_id = f.file_user_id');
		
		while($row = $db->fetch($result))
		{
			$row['file_orig_name'] = htmlentities($row['file_orig_name']);
			
			if($row['user_id'] == 0) {
				$row['user_name'] = $row['file_user_name'];
			}
			
			$row['file_description'] = htmlentities($row['file_description']);
			$row['file_upload_time'] = $this->convertTime($row['file_upload_time']);
			$row['user_name_raw']    = $row['user_name'];
			$row['user_name']        = htmlentities($row['user_name']);
			$row['file_size']        = $this->HRFileSize($row['file_size']);
			
			$files[] = $row;
		}
		
		$tpl->assign('files', $files);
	}
	
	/**
	 * Displays a single uploaded file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function uploadView()
	{
		if(!isset($this->get['fid']) || !ctype_digit($this->get['fid'])) {
			$this->uploadList();
			return false;
		}
		
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		$fileID = intval($this->get['fid']);
		$result = $db->query('SELECT f.*, u.user_id, u.user_name FROM '.DB_PREFIX.'uploads f '.
		'LEFT JOIN '.DB_PREFIX.'users u ON u.user_id = f.file_user_id '.
		'WHERE file_id = '.$fileID);
		
		if($db->numRows($result) != 1) {
			$this->uploadList();
			return false;
		}
		
		$file = $db->fetch($result);
		
		if($file['file_ext'] == 'gif' || $file['file_ext'] == 'jpg' || 
		   $file['file_ext'] == 'png' || $file['file_ext'] == 'jpeg') {
			$file['is_image'] = true;
		} else {
			$file['is_image'] = false;
		}
		
		if(isset($this->get['o'])) {
			$o = $this->get['o'];
			
			if($o == 'download') {
				$this->uploadSendFile($file);
				exit;
			}
		}
		
		$file['file_orig_name'] = htmlentities($file['file_orig_name']);
		
		if($file['file_user_id'] < 1) {
			$file['user_name'] = $file['file_user_name'];
		}
		
		$file['user_name_raw']    = $file['user_name'];
		$file['user_name']        = htmlentities($file['user_name']);
		$file['file_description'] = htmlentities($file['file_description']);
		$file['file_upload_time'] = $this->convertTime($file['file_upload_time']);
		$file['file_size']        = $this->HRFileSize($file['file_size']);
		
		$tpl->assign('file',      $file);
		$tpl->assign('versions',  $this->getFileVersions($file['file_id']));
		
		$this->uploadTemplate = 'special_uploads_file.tpl';
	}
	
	/**
	 * Loads the version information of all versions of an uploaded
	 * file from the database.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  int $fileID Internal (numeric) file id
	 * @return void
	 **/
	function getFileVersions($fileID)
	{
		$db = &singleton('database');
		
		$versions = array();
		$result   = $db->query('SELECT c.*, u.user_id, u.user_name '.
		'FROM '.DB_PREFIX.'uploads_changelog c '.
		'LEFT JOIN '.DB_PREFIX.'users u ON u.user_id = c.file_user_id '.
		'WHERE c.file_id = '.$fileID.' ORDER BY c.file_upload_time DESC');
		
		while($row = $db->fetch($result))
		{
			if($row['file_user_id'] < 1) {
				$row['user_name'] = $row['file_user_name'];
			}
			
			$row['user_name_raw']    = $row['user_name'];
			$row['user_name']        = htmlentities($row['user_name']);
			$row['file_upload_time'] = $this->convertTime($row['file_upload_time']);
			$row['file_size']        = $this->HRFileSize($row['file_size']);
			$row['file_description'] = htmlentities($row['file_description']);
			
			$versions[$row['file_version']] = $row;
		}
		
		return $versions;
	}
	
	/**
	 * If the user clicked the download link, this function
	 * sends the file as an attachment, so the browser will
	 * display the "Save as ..." dialog.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @param  array &$file Image information
	 * @return void
	 **/
	function uploadSendFile(&$file)
	{
		if(strpos('MSIE', $this->server['HTTP_USER_AGENT']) !== false) {
			header('Content-Type: application/octetstream');
			header('Content-Disposition: inline; filename="'.$file['file_orig_name'].'"');
		} else {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$file['file_orig_name'].'"');
		}
		
		header('Content-Length: '.$file['file_size']);
		
		if($file['ext'] == 'gif' || $file['ext'] == 'jpeg' ||
		   $file['ext'] == 'jpg' || $file['ext'] == 'png') {
			$fp = fopen($this->cfg['doc_root'].'/uploads/img/'.$file['file_id'], 'r');
		} else {
			$fp = fopen($this->cfg['doc_root'].'/uploads/other/'.$file['file_id'], 'r');
		}
		
		fpassthru($fp);
		
		return true;
	}
	
	/**
	 * Manages the upload of a new file.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function uploadNew()
	{
		$tpl = &singleton('template');
		
		if($this->cfg['enable_uploads'] != 1) {
			$tpl->assign('isMessage', true);
			$tpl->assign('message',   $this->lang['uploads_disabled']);
			return false;
		}
		
		$this->lang['uploads_new_desc'] = sprintf($this->lang['uploads_new_desc'], $this->cfg['upload_max_size'], join(', ', $this->uploadFileTypes));
		$this->uploadTemplate = 'special_uploads_new.tpl';
		
		if($this->request == 'POST') {
			$cStat = $this->checkConfirm();
			
			if($cStat == -1) {
				return false;
			}
			
			if($this->validateUpload()) {
				if($this->addUploadedFile()) {
					$tpl->assign('isMessage', true);
					$tpl->assign('message',   $this->lang['uploads_new_done']);
					
					$this->uploadList();
					$this->uploadTemplate = 'special_uploads.tpl';
				} else {
					$tpl->assign('isError', true);
					$tpl->assign('errors',  $this->uploadError);
					
					$this->uploadList();
					$this->uploadTemplate = 'special_uploads_new.tpl';
				}
			} else {
				$tpl->assign('isError', true);
				$tpl->assign('errors',  $this->uploadError);
			}
		}
	}
	
	/**
	 * Checks if the current post request is a confirmation
	 * which was requested because of a file conflict.
	 * If so, the function generates the $_FILES array out
	 * of the submited information (temporary file, original name).
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return int -1 on error, 0 if no confirm, 1 if confirm.
	 **/
	function checkConfirm()
	{
		if(!isset($this->get['confirm'])) {
			return 0;
		}
		
		$do        = isset($this->post['confirm_do']) ? trim($this->post['confirm_do']) : '';
		$fileLocal = isset($this->post['file_local']) ? trim($this->post['file_local']) : '';
		$fileOrig  = isset($this->post['file_orig'])  ? trim($this->post['file_orig'])  : '';
			
		if(!preg_match('/^[a-f0-9]{40}$/', $fileLocal) || $fileOrig == '') {
			return -1;
		}
		
		if(!file_exists($this->cfg['doc_root'].'/uploads/tmp/'.$fileLocal)) {
			return -1;
		}
		
		if($do == 'overwrite') {
			$this->fileConfirm = true;
			$this->fileAction  = 'overwrite';
			
			$this->files['upload']             = array();
			$this->files['upload']['name']     = $fileOrig;
			$this->files['upload']['tmp_name'] = $this->cfg['doc_root'].'/uploads/tmp/'.$fileLocal;
			$this->files['upload']['size']     = filesize($this->cfg['doc_root'].'/uploads/tmp/'.$fileLocal);
			$this->files['upload']['error']    = 0;
		} elseif($do == 'rename') {
			$newName = isset($this->post['new_name']) ? trim($this->post['new_name']) : '';
			
			if($newName == '') {
				return -1;
			}
			
			$this->fileConfirm = true;
			$this->fileAction  = 'rename';
			$this->fileOrig    = $fileOrig;
			
			$this->files['upload']             = array();
			$this->files['upload']['name']     = $newName;
			$this->files['upload']['tmp_name'] = $this->cfg['doc_root'].'/uploads/tmp/'.$fileLocal;
			$this->files['upload']['size']     = filesize($this->cfg['doc_root'].'/uploads/tmp/'.$fileLocal);
			$this->files['upload']['error']    = 0;
		} else {
			return -1;
		}
		
		return 1;
	}
	
	/**
	 * Validates an upload. Checks file size, type and
	 * uploader information.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return bool true if file is valid, false otherwise.
	 **/
	function validateUpload()
	{
		$db  = &singleton('database');
		$tpl = &singleton('template');
		
		if(!isset($this->files['upload'])) {
			return false;
		}
		
		$file    = &$this->files['upload'];
		$isError = false;
		$errors  = array();
		
		if($file['error'] != UPLOAD_ERR_OK) { // Upload error ...
			if($file['error'] == UPLOAD_ERR_INI_SIZE || $file['error'] == UPLOAD_ERR_FORM_SIZE) {
				$isError  = true;
				$errors[] = $this->lang['uploads_err_big_file'];
			} elseif($file['error'] == UPLOAD_ERR_PARTIAL) {
				$isError  = true;
				$errors[] = $this->lang['uploads_err_partial'];
			} elseif($file['error'] == UPLOAD_ERR_NO_FILE || !is_uploaded_file($file['name'])) {
				$isError  = true;
				$errors[] = $this->lang['uploads_err_no_file'];
			}
		}
		
		if($file['size'] > ($this->cfg['upload_max_size'] * 1024)) { // File is too big ...
			$isError  = true;
			$errors[] = $this->lang['uploads_err_big_file'];
		}
		
		if($file['size'] == 0) { // No file uploaded, zero-sized file ...
			$isError  = true;
			$errors[] = $this->lang['uploads_err_no_file'];;
		}
		
		$file['ext']   = preg_replace('/^.+?\.(?=[A-Za-z0-9]+$)/', '', $file['name']);
		$file['local'] = sha1($file['name']);
				
		// Now validate user name and file description.
		if(!in_array($file['ext'], $this->uploadFileTypes)) {
			$isError  = true;
			$errors[] = $this->lang['uploads_err_type'];
		}
		
		if($this->loggedIn) {
			$this->fileUserId   = $this->user['user_id'];
			$this->fileUserName = '';
		} else {
			$this->fileUserId   = 0;
			$this->fileUserName = isset($this->post['upload_user']) ? trim($this->post['upload_user']) : '';
			
			if($this->fileUserName != '' && is_array($this->getUser($this->fileUserName, true))) {
				$errors[] = $this->lang['edit_username_taken'];
				$isError  = true;
			}
		}
		
		$this->fileDesc    = isset($this->post['upload_desc']) ? substr(trim($this->post['upload_desc']), 0, 255) : '';
		
		/**
		 * Checks if an uploaded file already exists. Displays
		 * the confirmation dialog if we got a file conflict.
		 */
		$result = $db->query('SELECT * FROM '.DB_PREFIX.'uploads WHERE file_orig_name = "'.addslashes($file['name']).'"');
		
		if($db->numRows($result) > 0 && !$this->fileConfirm) {
			if(!move_uploaded_file($file['tmp_name'], $this->cfg['doc_root'].'/uploads/tmp/'.$file['local'])) {
				$isError  = true;
				$errors[] = $this->lang['uploads_err_move'];
			} else {
				$tpl->assign('fileLocalName', $file['local']);
				$tpl->assign('fileName',      $file['name']);
				$tpl->assign('fileUser',      htmlentities($this->fileUserName));
				$tpl->assign('fileDesc',      htmlentities($this->fileDesc));
				
				$this->uploadTemplate = 'special_uploads_confirm.tpl';
			}
			
			return false;
		}
		
		if($db->numRows($result) > 0) {
			$row = $db->fetch($result);
			$this->fileData = $row;
		}
		
		if($isError) {
			$tpl->assign('valUploadDesc', htmlentities($this->fileDesc));
			$tpl->assign('valUploadUser', htmlentities($this->fileUserName));
			
			if($this->loggedIn) {
				$tpl->assign('valUploadUser', htmlentities($this->user['user_name']));
			}
			
			$this->uploadError = $errors;
			return false;
		}
		
		return true;
	}
	
	/**
	 * Moves a uploaded and validated file into the wikis upload directory
	 * and inserts the files information into the database table for uploads.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function addUploadedFile()
	{
		$db   = &singleton('database');
		$tpl  = &singleton('template');
		
		$file    = &$this->files['upload'];
		$isError = false;
		$errors  = array();
		$isImage = false;
		
		if($file['ext'] == 'gif' || $file['ext'] == 'jpeg' ||
		   $file['ext'] == 'jpg' || $file['ext'] == 'png') {
			$isImage = true;
		}
		
		if($this->fileConfirm && $this->fileAction == 'overwrite') {
			if($isImage) {
				$oldFile = $this->cfg['doc_root'].'/uploads/img/'.$this->fileData['file_id'].'.'.$file['ext'];
			} else {
				$oldFile = $this->cfg['doc_root'].'/uploads/other/'.$this->fileData['file_id'];
			}
			
			if(!rename($oldFile, $oldFile.'.'.$this->fileData['file_version'])) {
				$errors[] = $this->lang['uploads_err_move'];
				$isError  = true;
			}
			
			if(!rename($file['tmp_name'], $oldFile)) {
				$errors[] = $this->lang['uploads_err_move'];
				$isError  = true;
			}
			
			$newVersion = $this->fileData['file_version'] + 1;
			
			$db->query('UPDATE '.DB_PREFIX.'uploads SET '.
			'file_size = '.$file['size'].', '.
			'file_user_id = '.$this->fileUserId.', '.
			'file_user_name = "'.addslashes($this->fileUserName).'", '.
			'file_description = "'.addslashes($this->fileDesc).'", '.
			'file_upload_time = '.$this->time.', '.
			'file_version = '.$newVersion.' '.
			'WHERE file_id = '.$this->fileData['file_id']);
			
			$db->query('INSERT INTO '.DB_PREFIX.'uploads_changelog(file_id, file_version, '.
			'file_size, file_user_id, file_user_name, file_description, file_upload_time) '.
			'VALUES('.$this->fileData['file_id'].', '.$newVersion.', '.$file['size'].', '.
			$this->fileUserId.', "'.addslashes($this->fileUserName).'", '.
			'"'.addslashes($this->fileDesc).'", '.$this->time.')');
			
			$this->uploadWriteThumb($this->fileData['file_id'], $file['ext']);
		} else {
			if($isImage) {
				$tmpFile = $this->cfg['doc_root'].'/uploads/img/0.'.$file['ext'];
				$newFile = $this->cfg['doc_root'].'/uploads/img/%s.'.$file['ext'];
			} else {
				$tmpFile = $this->cfg['doc_root'].'/uploads/other/0';
				$newFile = $this->cfg['doc_root'].'/uploads/other/%s';
			}
			
			if(!$this->fileConfirm) {
				if(!move_uploaded_file($file['tmp_name'], $tmpFile)) {
					$errors[] = $this->lang['uploads_err_move'];
					$isError  = true;
				}
			} else {
				if(!rename($file['tmp_name'], $tmpFile)) {
					$errors[] = $this->lang['uploads_err_move'];
					$isError  = true;
				}
			}
			
			$db->query('INSERT INTO '.DB_PREFIX.'uploads(file_orig_name, '.
			'file_ext, file_size, file_user_id, file_user_name, file_description, file_upload_time) '.
			'VALUES("'.addslashes($file['name']).'", "'.$file['ext'].'", '.
			$file['size'].', '.$this->fileUserId.', "'.addslashes($this->fileUserName).'", '.
			'"'.addslashes($this->fileDesc).'", '.$this->time.')');
			
			$fileId = $db->insertId();
			
			$db->query('INSERT INTO '.DB_PREFIX.'uploads_changelog(file_id, '.
			'file_size, file_user_id, file_user_name, file_description, file_upload_time) '.
			'VALUES('.$db->insertId().', '.$file['size'].', '.$this->fileUserId.', '.
			'"'.addslashes($this->fileUserName).'", "'.addslashes($this->fileDesc).'", '.
			$this->time.')');
			
			rename($tmpFile, sprintf($newFile, $fileId));
			$this->uploadWriteThumb($fileId, $file['ext']);
		}
		
		if($isError) {
			$this->uploadError = $errors;
			return false;
		}
		
		return true;
	}
	
	function uploadWriteThumb($fileId, $fileExt)
	{
		$info = gd_info();
		
		if($fileExt == 'gif') {
			if(!$info['GIF Read Support'] || !$info['GIF Create Support']) {
				return false;
			}
			
			$img  = imagecreatefromgif($this->cfg['doc_root'].'/uploads/img/'.$fileId.'.'.$fileExt);
			$type = 'gif';
		} elseif($fileExt == 'jpg' || $fileExt == 'jpeg') {
			if(!$info['JPG Support']) {
				return false;
			}
			
			$img  = imagecreatefromjpeg($this->cfg['doc_root'].'/uploads/img/'.$fileId.'.'.$fileExt);
			$type = 'jpg';
		} elseif($fileExt == 'png') {
			if(!$info['PNG Support']) {
				return false;
			}
			
			$img  = imagecreatefrompng($this->cfg['doc_root'].'/uploads/img/'.$fileId.'.'.$fileExt);
			$type = 'png';
		} else {
			return false;
		}
		
		$sx = imagesx($img);
		$sy = imagesy($img);
	
		if($sx > $sy) {
			$p  = $sx / $this->cfg['upload_thumb_size'];
			$tx = floor($sx / $p);
			$ty = floor($sy / $p);
		} elseif($sy > $sx) {
			$p  = $sy / $this->cfg['upload_thumb_size'];
			$tx = floor($sx / $p);
			$ty = floor($sy / $p);
		} else {
			return false;
		}
		
		if($type == 'gif') {
			$thumb = imagecreate($tx, $ty);
		} else {
			$thumb = imagecreatetruecolor($tx, $ty);
		}
		
		if($type == 'gif') {
	        	imagecopyresized($thumb, $img, 0, 0, 0, 0, $tx, $ty, $sx, $sy);
		} else {
			imagecopyresampled($thumb, $img, 0, 0, 0, 0, $tx, $ty, $sx, $sy);
		}
	
		if($type == 'gif') {
			imagegif($thumb, $this->cfg['doc_root'].'/uploads/img/thumbs/'.$fileId.'.'.$fileExt);
		} elseif($type == 'jpg') {
			imagejpeg($thumb, $this->cfg['doc_root'].'/uploads/img/thumbs/'.$fileId.'.'.$fileExt);
		} elseif($type == 'png') {
			imagepng($thumb, $this->cfg['doc_root'].'/uploads/img/thumbs/'.$fileId.'.'.$fileExt);
		}
	}
	
	/**
	 * Makes an array containing the allowed file extensions.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return void
	 **/
	function initFileTypesArray()
	{
		$this->uploadFileTypes = explode(',', $this->cfg['upload_file_types']);
	}
	
	/**
	 * Returns the template name for this special page.
	 *
	 * @author Johannes Klose <exe@calitrix.de>
	 * @return string Template name
	 **/
	function getTemplate()
	{
		return $this->uploadTemplate;
	}
}
?>