<?php
namespace page\bo;

use n2n\reflection\ObjectAdapter;
use n2n\reflection\annotation\AnnoInit;
use n2n\persistence\orm\annotation\AnnoOneToOne;
use n2n\persistence\orm\annotation\AnnoManyToOne;
use n2n\persistence\orm\annotation\AnnoOneToMany;
use n2n\persistence\orm\CascadeType;
use rocket\user\bo\RocketUser;
use n2n\l10n\N2nLocale;
use rocket\spec\ei\component\field\impl\translation\Translator;
use page\model\leaf\ExternalLeaf;
use page\model\nav\NavTree;
use page\model\leaf\InternalLeaf;
use page\model\IllegalPageStateException;
use page\model\leaf\ContentLeaf;
use page\model\nav\NavBranch;
use page\model\nav\UnknownNavBranchException;
use page\model\NavInitProcess;
use page\model\nav\ObjAffiliationTester;
use n2n\reflection\ArgUtils;
use page\model\PageMonitor;
use n2n\persistence\orm\annotation\AnnoEntityListeners;
use n2n\reflection\CastUtils;

class Page extends ObjectAdapter {
	const NS = 'page';
	
	private static function _annos(AnnoInit $ai) {
		$ai->c(new AnnoEntityListeners(PageEntityListener::getClass()));
		$ai->p('pageContent', new AnnoOneToOne(PageContent::getClass(), null, CascadeType::ALL, null, true));
		$ai->p('internalPage', new AnnoManyToOne(Page::getClass()));
		$ai->p('pageTs', new AnnoOneToMany(PageT::getClass(), 'page', CascadeType::ALL, null, true));
	}
		
	private $id;
	private $internalPage;
	private $externalUrl;
	private $pageContent;
	private $subsystemName;
	private $online = true;
	private $inPath = true;
	private $hookKey;
	private $inNavigation = true;
	private $navTargetNewWindow = false;
	private $lft;
	private $rgt;
	private $lastMod;
	private $lastModBy;
	private $pageTs;
	
	public function __construct() {
		$this->lastMod = new \DateTime();
	}

	private function _prePersist(PageMonitor $pageMonitor) {
		$pageMonitor->registerInsert($this);
	}
	
	private function _preUpdate(PageMonitor $pageMonitor) {
		$this->lastMod = new \DateTime();
		$pageMonitor->registerUpdate($this);
	}
	
	private function _preRemove(PageMonitor $pageMonitor) {
		$pageMonitor->registerRemove($this);
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getInternalPage() {
		return $this->internalPage;
	}
	
	public function setInternalPage($internalPage) {
		$this->internalPage = $internalPage;
	}
	
	public function getExternalUrl() {
		return $this->externalUrl;
	}
	
	public function setExternalUrl($externalUrl) {
		$this->externalUrl = $externalUrl;
	}
	
	public function getPageContent() {
		return $this->pageContent;
	}
	
	public function setPageContent(PageContent $pageContent = null) {
		$this->pageContent = $pageContent;
	}
	
	public function getSubsystemName() {
		return $this->subsystemName;
	}
	
	public function setSubsystemName(string $subsystemName = null) {
		$this->subsystemName = $subsystemName;
	}
	
	public function isOnline(): bool {
		return $this->online;
	}
	
	public function setOnline(bool $online) {
		$this->online = $online;
	}
	
	public function isInPath(): bool {
		return $this->inPath;
	}
	
	public function setInPath(bool $inPath) {
		$this->inPath = $inPath;
	}
	
	public function isInNavigation(): bool {
		return $this->inNavigation;
	}
	
	public function setInNavigation(bool $inNavigation) {
		$this->inNavigation = $inNavigation;
	}
	
	public function isNavTargetNewWindow(): bool {
		return $this->navTargetNewWindow;
	}
	
	public function setNavTargetNewWindow(bool $targetNewWindow) {
		$this->navTargetNewWindow = $targetNewWindow;
	}
	
	public function getHookKey() {
		return $this->hookKey;
	}
	
	public function setHookKey($hookKey) {
		$this->hookKey = $hookKey;
	}
	
	public function getLft() {
		return $this->lft;
	}
	
	public function setLft($lft) {
		$this->lft = $lft;
	}
	
	public function getRgt() {
		return $this->rgt;
	}
	
	public function setRgt($rgt) {
		$this->rgt = $rgt;
	}
	/**
	 *
	 * @return \DateTime
	 */
	public function getLastMod() {
		return $this->lastMod;
	}
	
	public function setLastMod(\DateTime $lastMod = null) {
		$this->lastMod = $lastMod;
	}
	/**
	 *
	 * @return \rocket\user\bo\RocketUser
	 */
	public function getLastModBy() {
		return $this->lastModBy;
	}
	
	public function setLastModBy(RocketUser $lastModBy) {
		$this->lastModBy = $lastModBy;
	}
	
	public function getPageTs() {
		return $this->pageTs;
	}
	
	public function setPageTs(\ArrayObject $pageTs) {
		$this->pageTs = $pageTs;
	}
	
	public function equals($obj) {
		return $obj instanceof Page && $this->id == $obj->getId();
	}
	
	/**
	 *
	 * @param N2nLocale ...$n2nLocales        	
	 * @return PageT
	 */
	public function t(N2nLocale ...$n2nLocales) {
		return Translator::findAny($this->pageTs, ...$n2nLocales);
	}
	
	/**
	 * @param NavInitProcess $navInitProcess
	 * @throws IllegalPageStateException
	 */
	public function createNavBranch(NavInitProcess $navInitProcess) {
		$navBranch = new NavBranch($navInitProcess->getNavTree(), $this->id);
		
		if ($this->hookKey !== null) {
			$navBranch->setHookKeys(array($this->hookKey));
		}
		
		$pageId = $this->getId();
		$navBranch->setObjAffiliationTester(new PageObjAffiliationTester($pageId));
		$navBranch->setInPath($this->isInPath());
		
		if ($this->externalUrl !== null) {
			$this->applyExternalLeafs($navBranch);
		} else if ($this->internalPage !== null) {
			$this->applyInternalLeafs($navBranch, $navInitProcess);
		} else if ($this->pageContent !== null) {
			$this->applyContentLeafs($navBranch);
		}
		
		return $navBranch; 
	}
	
	private function applyExternalLeafs(NavBranch $navBranch) {
		foreach ($this->pageTs as $pageT) {
			CastUtils::assertTrue($pageT instanceof PageT);
			
			$leaf = new ExternalLeaf($pageT->getN2nLocale(), $pageT->getName(), $this->externalUrl);
			$leaf->setAccessible($this->online && $pageT->isActive());
			$leaf->setPathPart($pageT->getPathPart());
			$leaf->setSubsystemName($pageT->getPage()->getSubsystemName());
			$leaf->setTitle($pageT->getTitle());
			$leaf->setInNavigation($leaf->isAccessible() && $this->inNavigation);
			$leaf->setTargetNewWindow($this->navTargetNewWindow);
			$navBranch->addLeaf($leaf);
		}
	}
	
	private function applyInternalLeafs(NavBranch $navBranch, NavInitProcess $navInitProcess) {
		$leafs = array();
		foreach ($this->pageTs as $pageT) {
			CastUtils::assertTrue($pageT instanceof PageT);
			
			$leafs[] = $leaf = new InternalLeaf($pageT->getN2nLocale(), $pageT->getName());
			$leaf->setAccessible($this->online && $pageT->isActive());
			$leaf->setPathPart($pageT->getPathPart());
			$leaf->setSubsystemName($pageT->getPage()->getSubsystemName());
			$leaf->setTitle($pageT->getTitle());
			$leaf->setInNavigation($leaf->isAccessible() && $this->inNavigation);
			$leaf->setTargetNewWindow($this->navTargetNewWindow);
			$navBranch->addLeaf($leaf);
		}
			
		$that = $this;
		$navInitProcess->onInitialized(function (NavTree $navTree) use ($that, $leafs) {
			try {
				$targetNavBranch = $navTree->get($that->internalPage);
				foreach ($leafs as $leaf) {
					$leaf->setTargetNavBranch($targetNavBranch);
				}
			} catch (UnknownNavBranchException $e) {
				throw new IllegalPageStateException('Internal link page (id: ' . $that->id 
						. ') contains invalid target.', 0, $e);
			}
		});
	}
	
	private function applyContentLeafs(NavBranch $navBranch) {
		$pageController = $this->pageContent->getPageController();
		$tagNames = $pageController->getTagNames();
		ArgUtils::valArrayReturn($tagNames, $pageController, 'getTagNames', array('scalar', null));
		$navBranch->setTagNames($tagNames);
		
		foreach ($this->pageTs as $pageT) {
			CastUtils::assertTrue($pageT instanceof PageT);
			
			$leafs[] = $leaf = new ContentLeaf($pageT->getN2nLocale(), $pageT->getName(), $this->id);
			$leaf->setAccessible($this->online && $pageT->isActive());
			$leaf->setPathPart($pageT->getPathPart());
			$leaf->setSubsystemName($pageT->getPage()->getSubsystemName());
			$leaf->setTitle($pageT->getTitle());
			$leaf->setInNavigation($leaf->isAccessible() && $this->inNavigation);
			$leaf->setTargetNewWindow($this->navTargetNewWindow);
			$navBranch->addLeaf($leaf);
		}
	}
}

class PageObjAffiliationTester implements ObjAffiliationTester {
	private $pageId;

	public function __construct($pageId) {
		$this->pageId = $pageId;
	}
	
	public function isAffiliatedWith($obj): bool {
		if ($obj instanceof PageController) {
			$obj = $obj->getPageContent();
		}
			
		if ($obj instanceof PageContent) {
			$obj = $obj->getPage();
		}
			
		if ($obj instanceof PageT) {
			if (!$obj->getN2nLocale()->equals($this->n2nLocale)) return false;
			$page = $obj->getPage();
		}
		
		return ($obj instanceof Page && $obj->getId() === $this->pageId);
	}
}