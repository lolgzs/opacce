/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage Zend_Auth_Adapter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: DbTable.php 8862 2008-03-16 15:36:00Z thomas $
 */

class Zend_Auth_Adapter_CommSIGB implements Zend_Auth_Adapter_Interface {
	protected $_identity = null;
	protected $_credential = null;

	public function setIdentity($identity) {
		$this->_identity = $identity;
	}

	public function setCredential($credential) {
		$this->_credential = $credential;
	}


	public function authenticate(){
		return $this->tryFetchUserFromSIGB($this->_identity, $this->_credential);
	}
	

	/**
	 * @return Class_Users
	 */
	public function tryFetchUserFromSIGB($login, $password) {
		$user = Class_Users::newInstance()
			->setLogin($login)
			->setPassword($password);

		$bibs = Class_IntBib::findAllWithWebServices();
		foreach($bibs as $bib) {
			if (!$emprunteur = $bib->getSIGBComm()->getEmprunteur($user))
				continue;

			if (!$emprunteur->isValid())
				continue;

			$user
				->beAbonneSIGB()
				->save();
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $login);
		}

		return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $login);
	}
}