<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library, Nooku\Component\Users;


/**
 * Captchable Controller Behavior
 *
 * @author  Arunas Mazeika <http://nooku.assembla.com/profile/arunasmazeika>
 * @package Component\Contacts
 */
class ContactsControllerBehaviorCaptchable extends Users\ControllerBehaviorCaptchable
{
    protected function _beforeRender(Library\ControllerContext $context)
    {
        $session = $context->user->getSession();
        if ($session->isActive())
        {
            $container = $session->getContainer('captcha');
            if ($container->has('data'))
            {
                // Push the captcha into the view.
                $this->getView()->captcha = $container->get('data');

                // Cleanup.
                $container->clear();
            }
        }
    }

    protected function _beforeAdd(Library\ControllerContext $context)
    {
        $result = parent::_beforeAdd($context);

        if (!$result)
        {
            $context->user->getSession()->getContainer('captcha')->set('data', $context->request->getData());
            $context->response->setRedirect($context->request->getReferrer(), $this->getCaptchaErrorMessage(), 'error');
        }

        return $result;
    }
}