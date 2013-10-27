<?php
/**
 * New Social Comments - плагин для социальных комментариев
 *
 * Версия:	1.0.0
 * Автор:	Александр Вереник
 * Профиль:	http://livestreet.ru/profile/Wasja/
 * GitHub:	https://github.com/wasja1982/livestreet_newsocialcomments
 *
 * Основан на плагинах:
 * 1) "OpenComments" (автор: flexbyte, модификации: iMind) - https://catalog.livestreetcms.com/addon/view/39/, https://github.com/iM1nd/opencomments
 * 2) "Social Comments" (автор: 4ever4you) - https://catalog.livestreetcms.com/addon/view/201/
 *
 **/

/**
 * Класс обработки URL'ов вида /blog/
 *
 */

class PluginNewsocialcomments_ActionBlog extends PluginNewsocialcomments_Inherit_ActionBlog
{

	/**
	 * Обработка добавление комментария к топику
	 *
	 * @return bool
	 */
	protected function SubmitComment() {

		/**
		 * Проверяем авторизован ли пользователь
		 */
		$guest = false;
		if (!$this->User_IsAuthorization()) {
			$this->oUserCurrent = $this->User_GetUserById(0);
			$guest = true;

			if (!Config::Get('plugin.newsocialcomments.enabled') && !getRequest("social")) {
				$this->Message_AddErrorSingle($this->Lang_Get('not_access'),$this->Lang_Get('error'));
				return;
			}

			/**
			* Проверяем на наличие aceAdminPanel, чтобы проверить IP адрес в бане
			*/
			if (class_exists('PluginAceadminpanel')) {
				$plugins = $this->Plugin_GetActivePlugins();
				if (in_array('aceadminpanel', $plugins)) {
					if ($this->PluginAceadminpanel_Admin_IsBanIp(func_getIp())) {
						$this->Message_AddErrorSingle($this->Lang_Get('adm_banned2_text'), $this->Lang_Get('error'));
						return;
					}
				}
			}

            if (!func_check(getRequest("guest_name"),"text",2,50)) {
                $this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_name'),$this->Lang_Get('error'));
                return;
            }

			if (!getRequest("social")) {
				if (Config::Get('plugin.newsocialcomments.ask_mail')) {
					if (!func_check(getRequest("guest_email"),"mail")) {
						$this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_mail'),$this->Lang_Get('error'));
						return;
					}
				}

				if (!isset($_SESSION['captcha_keystring']) or $_SESSION['captcha_keystring']!=strtolower(getRequest('captcha'))) {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_captcha'),$this->Lang_Get('error'));
					return;
				}
			} else {
				if (!func_check(getRequest("social_avatar"),"text",1,255) || !func_check(getRequest("social_profile"),"text",1,255)) {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_social'),$this->Lang_Get('error'));
					return;
				}

                if (!in_array(getRequest("social"), array("vk", "fb")) || (getRequest("social") == "vk" && !$this->checkVkAuth()) || (getRequest("social") == "fb" && !$this->checkFbAuth())) {
					$this->Message_AddErrorSingle($this->Lang_Get('plugin.newsocialcomments.newsocialcomments_error_auth'),$this->Lang_Get('error'));
					return;
                }
			}
		}

		/**
		 * Проверяем топик
		 */
		if (!($oTopic=$this->Topic_GetTopicById(getRequest('cmt_target_id')))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		 * Возможность постить коммент в топик в черновиках
		 */
		if (!$oTopic->getPublish() and $this->oUserCurrent->getId()!=$oTopic->getUserId() and !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		/**
		* Проверяем разрешено ли постить комменты
		*/
		if (!$guest and !$this->ACL_CanPostComment($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_acl'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверяем разрешено ли постить комменты по времени
		*/
		if (!$guest and !$this->ACL_CanPostCommentTime($this->oUserCurrent) and !$this->oUserCurrent->isAdministrator()) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_limit'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверяем запрет на добавления коммента автором топика
		*/
		if ($oTopic->getForbidComment()) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_notallow'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверяем текст комментария
		*/
		if ($guest) {
			$sText=nl2br(strip_tags(getRequest('comment_text')));
		} else {
			$sText=$this->Text_Parser(getRequest('comment_text'));
		}

		if (!func_check($sText,'text',2,10000)) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_add_text_error'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Проверям на какой коммент отвечаем
		*/
		$sParentId=(int)getRequest('reply');
		if (!func_check($sParentId,'id')) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}
		$oCommentParent=null;
		if ($sParentId!=0) {
			/**
			* Проверяем существует ли комментарий на который отвечаем
			*/
			if (!($oCommentParent=$this->Comment_GetCommentById($sParentId))) {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return;
			}
			/**
			* Проверяем из одного топика ли новый коммент и тот на который отвечаем
			*/
			if ($oCommentParent->getTargetId()!=$oTopic->getId()) {
				$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
				return;
			}
		} else {
			/**
			* Корневой комментарий
			*/
			$sParentId=null;
		}
		/**
		* Проверка на дублирующий коммент
		*/
		if ($this->Comment_GetCommentUnique($oTopic->getId(),'topic',$this->oUserCurrent->getId(),$sParentId,md5($sText))) {
			$this->Message_AddErrorSingle($this->Lang_Get('topic_comment_spam'),$this->Lang_Get('error'));
			return;
		}
		/**
		* Создаём коммент
		*/
		$oCommentNew=Engine::GetEntity('Comment');
		$oCommentNew->setTargetId($oTopic->getId());
		$oCommentNew->setTargetType('topic');
		$oCommentNew->setTargetParentId($oTopic->getBlog()->getId());
		$oCommentNew->setUserId($this->oUserCurrent->getId());
		$oCommentNew->setText($sText);
		$oCommentNew->setDate(date("Y-m-d H:i:s"));
		$oCommentNew->setUserIp(func_getIp());
		$oCommentNew->setPid($sParentId);
		$oCommentNew->setTextHash(md5($sText));
		$oCommentNew->setPublish($oTopic->getPublish());

		if ($guest) {
            $oCommentNew->setGuestName(getRequest("guest_name"));
            $oCommentNew->setGuestEmail(getRequest("guest_email"));
            $oCommentNew->setGuestAvatar(getRequest("social_avatar") ? getRequest("social_avatar") : null);
            $oCommentNew->setGuestProfile(getRequest("social_profile") ? getRequest("social_profile") : null);
            unset($_SESSION['captcha_keystring']);
        } else {
		    $oCommentNew->setGuestName(null);
            $oCommentNew->setGuestEmail(null);
		}

		/**
		* Добавляем коммент
		*/
		$this->Hook_Run('comment_add_before', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));
		if ($this->Comment_AddComment($oCommentNew)) {
			$this->Hook_Run('comment_add_after', array('oCommentNew'=>$oCommentNew,'oCommentParent'=>$oCommentParent,'oTopic'=>$oTopic));

			$this->Viewer_AssignAjax('sCommentId',$oCommentNew->getId());
			if ($oTopic->getPublish()) {
				/**
			 	* Добавляем коммент в прямой эфир если топик не в черновиках
			 	*/
				$oCommentOnline=Engine::GetEntity('Comment_CommentOnline');
				$oCommentOnline->setTargetId($oCommentNew->getTargetId());
				$oCommentOnline->setTargetType($oCommentNew->getTargetType());
				$oCommentOnline->setTargetParentId($oCommentNew->getTargetParentId());
				$oCommentOnline->setCommentId($oCommentNew->getId());

				$this->Comment_AddCommentOnline($oCommentOnline);
			}
			/**
			* Сохраняем дату последнего коммента для юзера
			*/
			$this->oUserCurrent->setDateCommentLast(date("Y-m-d H:i:s"));
			$this->User_Update($this->oUserCurrent);
			/**
			* Отправка уведомления автору топика
			*/
			$oUserTopic=$oTopic->getUser();
			if ($oCommentNew->getUserId()!=$oUserTopic->getId()) {
				$this->Notify_SendCommentNewToAuthorTopic($oUserTopic,$oTopic,$oCommentNew,$this->oUserCurrent);
			}
			/**
			* Отправляем уведомление тому на чей коммент ответили
			*/
			if ($oCommentParent and $oCommentParent->getUserId()!=$oTopic->getUserId() and $oCommentNew->getUserId()!=$oCommentParent->getUserId()) {
				$oUserAuthorComment=$oCommentParent->getUser();
				$this->Notify_SendCommentReplyToAuthorParentComment($oUserAuthorComment,$oTopic,$oCommentNew,$this->oUserCurrent);
			}

            /**
             * Добавляем событие в ленту
             */
            $this->Stream_write($oCommentNew->getUserId(), 'add_comment', $oCommentNew->getId(), $oTopic->getPublish() && $oTopic->getBlog()->getType()!='close');
		} else {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
		}
	}

	/**
	 * Получение новых комментариев
	 *
	 */
	protected function AjaxResponseComment() {

		$this->Viewer_SetResponseAjax('json');
		/**
		 * Пользователь авторизован?
		 */
		if (!$this->oUserCurrent) {
			$this->oUserCurrent = $this->User_GetUserById(0);
		}
		/**
		 * Топик существует?
		 */
		$idTopic=getRequestStr('idTarget',null,'post');
		if (!($oTopic=$this->Topic_GetTopicById($idTopic))) {
			$this->Message_AddErrorSingle($this->Lang_Get('system_error'),$this->Lang_Get('error'));
			return;
		}

		$idCommentLast=getRequestStr('idCommentLast',null,'post');
		$selfIdComment=getRequestStr('selfIdComment',null,'post');
		$aComments=array();
		/**
		 * Если используется постраничность, возвращаем только добавленный комментарий
		 */
		if (getRequest('bUsePaging',null,'post') and $selfIdComment or $this->oUserCurrent->getUserId()==0) {
			if ($oComment=$this->Comment_GetCommentById($selfIdComment) and $oComment->getTargetId()==$oTopic->getId() and $oComment->getTargetType()=='topic') {
				$oViewerLocal=$this->Viewer_GetLocalViewer();
				$oViewerLocal->Assign('oUserCurrent',$this->oUserCurrent);
				$oViewerLocal->Assign('bOneComment',true);

				$oViewerLocal->Assign('oComment',$oComment);
				$sText=$oViewerLocal->Fetch($this->Comment_GetTemplateCommentByTarget($oTopic->getId(),'topic'));
				$aCmt=array();
				$aCmt[]=array(
					'html' => $sText,
					'obj'  => $oComment,
				);
			} else {
				$aCmt=array();
			}
			$aReturn['comments']=$aCmt;
			$aReturn['iMaxIdComment']=$selfIdComment;
		} else {
			$aReturn=$this->Comment_GetCommentsNewByTargetId($oTopic->getId(),'topic',$idCommentLast);
		}
		$iMaxIdComment=$aReturn['iMaxIdComment'];

		$oTopicRead=Engine::GetEntity('Topic_TopicRead');
		$oTopicRead->setTopicId($oTopic->getId());
		$oTopicRead->setUserId($this->oUserCurrent->getId());
		$oTopicRead->setCommentCountLast($oTopic->getCountComment());
		$oTopicRead->setCommentIdLast($iMaxIdComment);
		$oTopicRead->setDateRead(date("Y-m-d H:i:s"));
		$this->Topic_SetTopicRead($oTopicRead);

		$aCmts=$aReturn['comments'];
		if ($aCmts and is_array($aCmts)) {
			foreach ($aCmts as $aCmt) {
				$aComments[]=array(
					'html' => $aCmt['html'],
					'idParent' => $aCmt['obj']->getPid(),
					'id' => $aCmt['obj']->getId(),
				);
			}
		}

		$this->Viewer_AssignAjax('iMaxIdComment',$iMaxIdComment);
		$this->Viewer_AssignAjax('aComments',$aComments);
	}

	/**
	 * Проверка авторизации Вконтакте
	 * http://vk.com/dev/openapi_auth
	 */
    function checkVkAuth() {
        $keys = array('expire', 'mid', 'secret', 'sid');
        $vk_id = Config::Get('plugin.newsocialcomments.vk_id');
        $vk_secret = Config::Get('plugin.newsocialcomments.vk_secret');
        $session = array();
        if (isset($_COOKIE['vk_app_' . $vk_id])) {
            $vk_cookie = $_COOKIE['vk_app_' . $vk_id];
            $session_data = explode ('&', $vk_cookie, 10);
            foreach ($session_data as $pair) {
                list($key, $value) = explode('=', $pair, 2);
                if (!empty($key) && !empty($value)) {
                    if ($key === 'sig') $sig = $value;
                    elseif (in_array($key, $keys)) $session[$key] = $value;
                }
            }
            foreach ($keys as $key) {
                if (!isset($session[$key])) return false;
            }
            ksort($session);
            $sign = '';
            foreach ($session as $key => $value) {
                $sign .= ($key . '=' . $value);
            }
            $sign = md5($sign . $vk_secret);
            if ($sig === $sign && $session['expire'] > time()) {
                return $session;
            }
        }
        return false;
    }

	/**
	 * Проверка авторизации Facebook
	 * https://developers.facebook.com/docs/facebook-login/using-login-with-games/
	 */
    function checkFbAuth() {
        $fb_id = Config::Get('plugin.newsocialcomments.fb_id');
        $fb_secret = Config::Get('plugin.newsocialcomments.fb_secret');
        $session = array();
        if (isset($_COOKIE['fbsr_' . $fb_id])) {
            $fb_cookie = $_COOKIE['fbsr_' . $fb_id];
            list($encoded_sig, $session_data) = explode('.', $fb_cookie, 2);
            $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
            $session = json_decode(base64_decode(strtr($session_data, '-_', '+/')), true);
            $sign = hash_hmac('sha256', $session_data, $fb_secret, $raw = true);
            if ($sig === $sign) {
                return $session;
            }
        }
        return false;
    }
}
?>