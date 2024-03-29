<?php
/**
 * Cali is based off of the Nimbus skin. As such, there may still be remnants of ShoutWiki stuff in the code.
 * These remnants will be either repurposed or removed.
 * 
 * Notes:
 * Template:Didyouknow is a part of the interface (=should be fully protected on the wiki)
 * If SocialProfile extension (+some other social extensions) is available,
 * then more stuff will appear in the skin interface.
 *
 * @file 
 * @author FairPlay137
 * @copyright Copyright © 2019
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}
use MediaWiki\MediaWikiServices;

/**
 * Main skin class.
 * @ingroup Skins
 */
class CaliTemplate extends BaseTemplate {
	/**
	 * @var Skin
	 */
	public $skin;
	
	/** @var string $mPersonalTools Saves the personal Tools */
	private $mPersonalTools = '';
	/** @var string $mPersonalToolsEcho Saves Echo notifications */
	private $mPersonalToolsEcho = '';
	/** @var string $mPersonalToolsLang Saves Universal Language Selector */
	private $mPersonalToolsLang = '';

	/**
	 * Should we show the page title (the <h1> HTML element) for the current
	 * page or not?
	 *
	 * @see /skins/Games/Games.skin.php, GamesTemplate::pageTitle()
	 *
	 * @return bool
	 */
	function showPageTitle() {
		$nsArray = array();
		// Suppress page title on NS_USER when SocialProfile ext. is installed
		if ( class_exists( 'UserProfile' ) ) {
			$nsArray[] = NS_USER;
		}
		// Also suppress page titles on social profiles (for users whose "main"
		// user page is the wiki-style page)
		if ( defined( 'NS_USER_PROFILE' ) ) {
			$nsArray[] = NS_USER_PROFILE;
		}
		// Finally do the opposite to the above for users whose main user page
		// is their social profile
		if ( defined( 'NS_USER_WIKI' ) ) {
			$nsArray[] = NS_USER_WIKI;
		}

		// Strangely enough this does *not* cause any errors even if $nsArray
		// is empty...I was sure it'd cause one.
		return (bool)!in_array( $this->skin->getTitle()->getNamespace(), $nsArray );
	}

	/**
	 * Template filter callback for Cali skin.
	 * Takes an associative array of data set from a SkinTemplate-based
	 * class, and a wrapper for MediaWiki's localization database, and
	 * outputs a formatted page.
	 */
	public function execute() {
		global $wgContLang, $wgLogo, $wgOut, $wgStylePath;
		global $wgLangToCentralMap;
		global $wgUserLevels;

		$this->skin = $this->data['skin'];

		$user = $this->skin->getUser();
		$services = MediaWikiServices::getInstance();
		$contLang = $services->getContentLanguage();
		$linkRenderer = $services->getLinkRenderer();

		$register_link = SpecialPage::getTitleFor( 'Userlogin', 'signup' );
		$login_link = SpecialPage::getTitleFor( 'Userlogin' );
		$logout_link = SpecialPage::getTitleFor( 'Userlogout' );
		$profile_link = Title::makeTitle( NS_USER, $user->getName() );
		$main_page_link = Title::newMainPage();
		$recent_changes_link = SpecialPage::getTitleFor( 'Recentchanges' );
		$top_fans_link = SpecialPage::getTitleFor( 'TopUsers' );
		$special_pages_link = SpecialPage::getTitleFor( 'Specialpages' );

		$help_link = $this->skin->helpLink();

		$upload_file = SpecialPage::getTitleFor( 'Upload' );
		$what_links_here = SpecialPage::getTitleFor( 'Whatlinkshere' );
		$preferences_link = SpecialPage::getTitleFor( 'Preferences' );
		$watchlist_link = SpecialPage::getTitleFor( 'Watchlist' );

		$skin = $this->getSkin();
		// User name (or "Guest") to be displayed at the top right (on LTR
		// interfaces) portion of the skin
		$user = $skin->getUser();
		if ( $user->isLoggedIn() ) {
			$userNameTop = htmlspecialchars( $user->getName(), ENT_QUOTES );
		} else {
			$userNameTop = $skin->msg( 'cali-not-logged-in' )->text();
		}
		
		$more_wikis = $this->buildMoreWikis();
		$navtabs = $this->buildNavtabs();
		
		$personalTools = $this->getPersonalTools();
		
		foreach ( $personalTools as $key => $item ) {
			if ( $key === 'notifications-notice' ) {
				$this->mPersonalToolsEcho .= $this->makeListItem( $key, $item );
			} else {
				if ( $key === 'notifications-alert' ) {
					$this->mPersonalToolsEcho .= $this->makeListItem( $key, $item );
				} else {
					if ( $key === 'uls' ) {
						$this->mPersonalToolsLang .= $this->makeListItem( $key, $item );
					} else {
						$this->mPersonalTools .= $this->makeListItem( $key, $item );
					}
				}
			}
		}

		$this->html( 'headelement' );
?><div id="container">
	<header id="header" class="noprint">
		<div id="tp-logo">
			<a href="<?php echo wfMessage( 'cali-branding-link-url' )->plain() ?>">
				<img  src="<?php echo $wgStylePath ?>/Cali/resources/img/samlogo.png" alt="" />
				<span id="tp-logotext"><?php echo wfMessage( 'cali-branding' )->plain() ?></span>
			</a>
		</div>
		<?php if ( $more_wikis ) { ?>
		<div id="tp-more-category">
			<div class="more-wikis-tab"><span><?php echo wfMessage( 'cali-more-wikis' )->plain() ?></span></div>
		</div>
		<div id="more-wikis-menu" style="display:none;">
		<?php
		$x = 1;
		foreach ( $more_wikis as $link ) {
			$ourClass = '';
			if ( $x == count( $more_wikis ) ) {
				$ourClass = ' class="border-fix"';
			}
			echo "<a href=\"{$link['href']}\"" . $ourClass .
				">{$link['text']}</a>\n";
			if ( $x > 1 && $x % 2 == 0 ) {
				echo '<div class="cleared"></div>' . "\n";
			}
			$x++;
		}
		?>
		</div><!-- #more-wikis-menu -->
		<?php } // if $more_wikis ?>
		<?php if ( $navtabs ) { ?>
		<?php if ( $more_wikis ) { ?>
		<div id="tp-navtab-container2">
		<?php } else { ?>
		<div id="tp-navtab-container">
		<?php } ?>
			<?php
			$x = 1;
			foreach ( $navtabs as $link ) {
				echo "<div class=\"navtab\"><a href=\"{$link['href']}\">{$link['text']}</a></div>\n";
				$x++;
			}
			?>
		</div>
		
		<?php } // if $navtabs ?>
		<div id="wiki-login">
			<div class="user-icon-container user-dmenu">
				<span id="username-top">
					<span id="username-text"><?php echo $userNameTop ?></span>
					<span class="username-space spacer"> </span>
					<span id="userIcon20"><?php echo $this->getAvatar( 20 ) ?></span>
					<span class="spacer"> </span>
					<span id="userIcon40"><?php echo $this->getAvatar( 40 ) ?></span>
				</span>
				<div class="user-dmenu-content">
					<!--<?php
					if ( $user->isLoggedIn() ) {
						echo '<a class="user-dmenu-option" href="' . htmlspecialchars( $profile_link->getFullURL() ) . '" rel="nofollow"><span>' . wfMessage( 'cali-profile' )->plain() . '</span></a>
						<a class="user-dmenu-option user-dmenu-option-redgradient" href="' . htmlspecialchars( $logout_link->getFullURL() ) . '"><span>' . wfMessage( 'cali-logout' )->plain() . '</span></a>';
					} else {
						echo '<a class="user-dmenu-option user-dmenu-option-signup" href="' . htmlspecialchars( $register_link->getFullURL() ) . '" rel="nofollow"><span>' . wfMessage( 'cali-signup' )->plain() . '</span></a>
						<a class="user-dmenu-option" href="' . htmlspecialchars( $login_link->getFullURL() ) . '" id="caliLoginButton"><span>' . wfMessage( 'cali-login' )->plain() . '</span></a>';
					}?>-->
					<?php echo $this->mPersonalTools; ?>
				</div>
			</div>
		</div><!-- #wiki-login -->
		
		<div id="echoNotifications">
			<ul>
				<?php echo $this->mPersonalToolsEcho; ?>
			</ul>
		</div>
		
		<div id="universalLanguageSelector">
			<ul>
				<?php echo $this->mPersonalToolsLang; ?>
			</ul>
		</div>
		
	</header><!-- #header -->
	
	<div id="main-body">
		<aside id="side-bar" class="noprint">
			<div id="site-header" class="noprint">
				<div id="site-logo">
					<a href="<?php echo htmlspecialchars( $main_page_link->getFullURL() ) ?>" title="<?php echo Linker::titleAttrib( 'p-logo', 'withaccess' ) ?>" accesskey="<?php echo Linker::accesskey( 'p-logo' ) ?>" rel="nofollow">
						<img src="<?php echo $wgLogo ?>" alt="" />
					</a>
				</div>
			</div>
			
			<div id="navigation">
				<div id="navigation-title"><?php echo wfMessage( 'navigation' )->plain() ?></div>
				<?php
					$this->navmenu_array = array();
					$this->navmenu = $this->getNavigationMenu();
					echo $this->printMenu( 0 );
				?>
				<div id="other-links-container">
					<div id="other-links">
					<?php
						// TODO: Add optional Upload File(s) link and Create Page link (CreatePageUw)
						
						// Only show the link to Special:TopUsers if wAvatar class exists and $wgUserLevels is an array
						if ( class_exists( 'wAvatar' ) && is_array( $wgUserLevels ) ) {
							echo '<a href="' . htmlspecialchars( $top_fans_link->getFullURL() ) . '">' . wfMessage( 'topusers' )->plain() . '</a>';
						}

						echo Linker::link(
							$recent_changes_link,
							wfMessage( 'recentchanges' )->text(),
							array(
								'title' => Linker::titleAttrib( 'n-recentchanges', 'withaccess' ),
								'accesskey' => Linker::accesskey( 'n-recentchanges' )
							)
						) . "\n" .
						'<div class="cleared"></div>' . "\n";

						if ( $user->isLoggedIn() ) {
							echo Linker::link(
								$watchlist_link,
								wfMessage( 'watchlist' )->text(),
								array(
									'title' => Linker::titleAttrib( 'pt-watchlist', 'withaccess' ),
									'accesskey' => Linker::accesskey( 'pt-watchlist' )
								)
							) . "\n" .
							Linker::link(
								$preferences_link,
								wfMessage( 'preferences' )->text(),
								array(
									'title' => Linker::titleAttrib( 'pt-preferences', 'withaccess' ),
									'accesskey' => Linker::accesskey( 'pt-preferences' )
								)
							) .
							'<div class="cleared"></div>' . "\n";
						}

						echo $help_link;
						?>
						<a href="<?php echo htmlspecialchars( $special_pages_link->getFullURL() ) ?>"><?php echo wfMessage( 'specialpages' )->plain() ?></a>
						<div class="cleared"></div>
					</div>
				</div>
			</div>
			<div id="search-box">
				<form method="get" action="<?php echo $this->text( 'wgScript' ) ?>" name="search_form" id="searchform">
					<input id="searchInput" type="text" class="search-field" name="search" value="" placeholder="<?php echo wfMessage( 'cali-search', $GLOBALS['wgSitename'] )->plain() ?>" /><br /><hr />
					<input type="submit" name="go" class="mw-skin-cali-button positive-button go-button" value="<?php echo wfMessage( 'go' ); ?>" />
					<input type="submit" name="fulltext" class="mw-skin-cali-button negative-button search-button" value="<?php echo wfMessage( 'search' ); ?>" />
					<label><?php echo wfMessage( 'cali-search-label' )->plain() ?></label>
				</form>
				<div class="cleared"></div>
			</div>
				<div class="bottom-left-nav">
					<?php
					// Hook point for TTSCWikiChat (also could be used for an ad engine? We have no plans to implement ads into the official TTSC Wikis, but the engine could be used for other wikis.)
					Hooks::run( 'CaliLeftSide' );

					if ( class_exists( 'RandomGameUnit' ) ) {
						// @note The CSS for this is loaded in SkinCali::prepareQuickTemplate();
						// it *cannot* be loaded here!
						echo RandomGameUnit::getRandomGameUnit();
					}
					$dykTemplate = Title::makeTitle( NS_TEMPLATE, 'Didyouknow' );
					if ( $dykTemplate->exists() ) {
					?>
						<div class="bottom-left-nav-container" id="cali-didyouknow">
							<h2><?php echo wfMessage( 'cali-didyouknow' )->plain() ?></h2>
							<?php echo $wgOut->parseAsInterface( '{{Didyouknow}}' ) ?>
						</div>
					<?php
					}
					
					echo $this->getInterlanguageLinksBox();

					if ( class_exists( 'RandomImageByCategory' ) ) {
						$randomImage = $wgOut->parseAsInterface(
							'<randomimagebycategory width="200" categories="Featured Image" />',
							false
						);
						echo '<div class="bottom-left-nav-container" id="cali-featuredimage">
						<h2>' . wfMessage( 'cali-featuredimage' )->plain() . '</h2>' .
						$randomImage . '</div>';
					}

					if ( class_exists( 'RandomFeaturedUser' ) ) {
						echo '<div class="bottom-left-nav-container" id="cali-featureduser">
							<h2>' . wfMessage( 'cali-featureduser' )->plain() . '</h2>' .
							$this->get( 'cali-randomfeatureduser' ) . '</div>';
					}
				?>
			</div>
		</aside>
		<div id="body-container">
			<?php if ( $this->data['sitenotice'] and $this->config->get( 'CaliSiteNoticeOutsideArticle' )) { ?><div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div><?php } ?>
			<div id="article">
				<?php echo $this->actionBar(); echo "\n"; ?>
				<div id="content">
					<main id="article-body" class="mw-body-content">
						<?php if ( $this->data['sitenotice'] and !($this->config->get( 'CaliSiteNoticeOutsideArticle' ))) { ?><div id="siteNotice" style="padding: 0.8em !important"><?php $this->html( 'sitenotice' ) ?></div><?php } ?>
						<div id="article-text" class="clearfix">
							<?php echo $this->getIndicators(); ?>
							<?php if ( $this->showPageTitle() ) { ?><h1 class="pagetitle"><?php $this->html( 'title' ) ?></h1><?php } ?>
							<p class='subtitle'><?php $this->msg( 'tagline' ) ?></p>
							<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
							<?php if ( $this->data['undelete'] ) { ?><div id="contentSub2"><?php $this->html( 'undelete' ) ?></div><?php } ?>
							<?php if ( $this->data['newtalk'] ) { ?><div class="usermessage"><?php $this->html( 'newtalk' ) ?></div><?php } ?>
							<!-- start content -->
							<?php $this->html( 'bodytext' ) ?>
							<?php $this->html( 'debughtml' ); ?>
							<?php if ( $this->data['catlinks'] ) { $this->html( 'catlinks' ); } ?>
							<!-- end content -->
							<?php if ( $this->data['dataAfterContent'] ) { $this->html( 'dataAfterContent' ); } ?>
						</div>
					</main>
				</div>
			</div>
		</div>
		<?php echo $this->footer(); ?>
	</div> <!-- #content -->
</div><!-- #container -->
<?php
		$this->printTrail();
		echo "\n";
		echo Html::closeElement( 'body' );
		echo "\n";
		echo Html::closeElement( 'html' );
	} // end of execute() method

	/**
	 * Parse MediaWiki-style messages called 'v3sidebar' to array of links,
	 * saving hierarchy structure.
	 * Message parsing is limited to first 150 lines only.
	 */
	private function getNavigationMenu() {
		$message_key = 'cali-sidebar';
		$message = trim( wfMessage( $message_key )->text() );

		if ( wfMessage( $message_key )->isDisabled() ) {
			return array();
		}

		$lines = array_slice( explode( "\n", $message ), 0, 150 );

		if ( count( $lines ) == 0 ) {
			return array();
		}

		$nodes = array();
		$nodes[] = array();
		$lastDepth = 0;
		$i = 0;
		foreach ( $lines as $line ) {
			# ignore empty lines
			if ( strlen( $line ) == 0 or substr( $line, 0, 1 ) === "<" ) {
				continue;
			}

			$node = $this->parseItem( $line );
			$node['depth'] = strrpos( $line, '*' ) + 1;

			if ( $node['depth'] == $lastDepth ) {
				$node['parentIndex'] = $nodes[$i]['parentIndex'];
			} elseif ( $node['depth'] == $lastDepth + 1 ) {
				$node['parentIndex'] = $i;
			} elseif (
				// ignore crap that works on Monobook, but not on other skins
				$node['text'] == 'SEARCH' ||
				$node['text'] == 'TOOLBOX' ||
				$node['text'] == 'LANGUAGES'
			)
			{
				continue;
			} else {
				for ( $x = $i; $x >= 0; $x-- ) {
					if ( $x == 0 ) {
						$node['parentIndex'] = 0;
						break;
					}
					if ( $nodes[$x]['depth'] == $node['depth'] - 1 ) {
						$node['parentIndex'] = $x;
						break;
					}
				}
			}

			$nodes[$i + 1] = $node;
			$nodes[$node['parentIndex']]['children'][] = $i+1;
			$lastDepth = $node['depth'];
			$i++;
		}

		return $nodes;
	}

	/**
	 * Extract the link text and destination (href) from a MediaWiki message
	 * and return them as an array.
	 *
	 * @param string $line Line from the sidebar message, such as ** mainpage|mainpage-description
	 * @return array Array containing the 'text' (description) and 'href' (target URL) keys
	 */
	private function parseItem( $line ) {
		$href = false;

		// trim spaces and asterisks from line and then split it to maximum two chunks
		$line_temp = explode( '|', trim( $line, '* ' ), 2 );

		// $line_temp now contains page name or URL as the 0th array element
		// and the link description as the 1st array element
		if ( count( $line_temp ) >= 2 && $line_temp[1] != '' ) {
			$msgObj = wfMessage( $line_temp[0] );
			$link = ( $msgObj->isDisabled() ? $line_temp[0] : trim( $msgObj->inContentLanguage()->text() ) );
			$textObj = wfMessage( trim( $line_temp[1] ) );
			$line = ( !$textObj->isDisabled() ? $textObj->text() : trim( $line_temp[1] ) );
		} else {
			$line = $link = trim( $line_temp[0] );
		}

		// Determine what to show as the human-readable link description
		if ( wfMessage( $line )->isDisabled() ) {
			// It's *not* the name of a MediaWiki message, so display it as-is
			$text = $line;
		} else {
			// Guess what -- it /is/ a MediaWiki message!
			$text = wfMessage( $line )->text();
		}

		if ( $link != null ) {
			if ( wfMessage( $line_temp[0] )->isDisabled() ) {
				$link = $line_temp[0];
			}
			if ( preg_match( '/^(?:' . wfUrlProtocols() . ')/', $link ) ) {
				$href = $link;
			} else {
				$title = Title::newFromText( $link );
				if ( $title ) {
					$title = $title->fixSpecialName();
					$href = $title->getLocalURL();
				} else {
					$href = '#';
				}
			}
		}

		return array(
			'text' => $text,
			'href' => $href
		);
	}

	/**
	 * Generate and return "More Wikis" menu, showing links to related wikis.
	 *
	 * @return Array: "More Wikis" menu
	 */
	private function buildMoreWikis() {
		$messageKey = 'morewikis';
		$message = trim( wfMessage( $messageKey )->text() );

		if ( wfMessage( $messageKey )->isDisabled() ) {
			return array();
		}

		$lines = array_slice( explode( "\n", $message ), 0, 150 );

		if ( count( $lines ) == 0 ) {
			return array();
		}

		foreach ( $lines as $line ) {
			$moreWikis[] = $this->parseItem( $line );
		}

		return $moreWikis;
	}
	
	/**
	 * Generate a navtabs array for use in the Navtab menus
	 *
	 * @return Array: "More Wikis" menu
	 */
	private function buildNavtabs() {
		$messageKey = 'cali-navtabs';
		$message = trim( wfMessage( $messageKey )->text() );

		if ( wfMessage( $messageKey )->isDisabled() ) {
			return array();
		}

		$lines = array_slice( explode( "\n", $message ), 0, 150 );

		if ( count( $lines ) == 0 ) {
			return array();
		}

		foreach ( $lines as $line ) {
			$navTabs[] = $this->parseItem( $line );
		}

		return $navTabs;
	}


	/**
	 * Prints the sidebar menu & all necessary JS
	 */
	private function printMenu( $id, $last_count = '', $level = 0 ) {
		global $wgContLang, $wgStylePath;

		$menu_output = '';
		$output = '';
		$count = 1;

		if ( isset( $this->navmenu[$id]['children'] ) ) {
			if ( $level ) {
				$menu_output .= '<div class="sub-menu'.( ( $this->config->get( 'CaliEnableExperimental3D' )) ? ' menu3d' : '' ).'" id="sub-menu' . $last_count . '" style="display:none;">';
			}
			foreach ( $this->navmenu[$id]['children'] as $child ) {
				$menu_output .= "\n\t\t\t\t" . '<div class="' . ( $level ? 'sub-' : '' ) . 'menu-item' .
				( ( $this->config->get( 'CaliEnableExperimental3D' )) ? ' menu-item-3d' : '' ) .
					( ( $count == sizeof( $this->navmenu[$id]['children'] ) ) ? ' border-fix' : '' ) .
					'" id="' . ( $level ? 'sub-' : '' ) . 'menu-item' .
						( $level ? $last_count . '_' : '_' ) . $count . '">';
				$menu_output .= "\n\t\t\t\t\t" . '<a id="' . ( $level ? 'a-sub-' : 'a-' ) . 'menu-item' .
					( $level ? $last_count . '_' : '_' ) . $count . '" href="' .
					( !empty( $this->navmenu[$child]['href'] ) ? htmlspecialchars( $this->navmenu[$child]['href'] ) : '#' ) . '">';

				$menu_output .= $this->navmenu[$child]['text'];
				// If a menu item has submenus, show an arrow so that the user
				// knows that there are submenus available
				if (
					isset( $this->navmenu[$child]['children'] ) &&
					sizeof( $this->navmenu[$child]['children'] )
				)
				{
					$menu_output .= '<img src="' . $wgStylePath . '/Cali/resources/img/right_arrow' .
						( $wgContLang->isRTL() ? '_rtl' : '' ) .
						'.gif" alt="" class="sub-menu-button" />';
				}
				$menu_output .= '</a>';
				//$menu_output .= $id . ' ' . sizeof( $this->navmenu[$child]['children'] ) . ' ' . $child . ' ';
				$menu_output .= $this->printMenu( $child, $last_count . '_' . $count, $level + 1 );
				//$menu_output .= 'last';
				$menu_output .= '</div>';
				$count++;
			}
			if ( $level ) {
				$menu_output .= '</div>';
			}
		}

		if ( $menu_output != '' ) {
			$output .= "<div id=\"menu{$last_count}\">";
			$output .= $menu_output;
			$output .= "</div>\n";
		}

		return $output;
	}

	/**
	 * Builds the content for the top navigation tabs (edit, history, etc.).
	 *
	 * @return array
	 */
	function buildActionBar() {
		$skin = $this->skin;
		$request = $skin->getRequest();
		$user = $skin->getUser();
		/**
		 * This function originally used to use $wgTitle, which worked
		 * relatively fine.
		 * Then it was reported then when you view a redirect, the edit tabs do
		 * not point to the page that you were redirected _to_, but rather to
		 * the page where you were redirected _from_.
		 * This issue was solved by swapping $wgTitle to this context-sensitive
		 * variable.
		 *
		 * @see http://bugzilla.shoutwiki.com/show_bug.cgi?id=224
		 */
		$title = $skin->getTitle();

		$content_actions = [];

		// Oh hey, this one's protected...
		$r = new ReflectionMethod( $skin, 'buildContentNavigationUrls' );
		$r->setAccessible( true );
		$content_navigation = $r->invoke( $skin );
		// In an ideal world this would Just Work(TM):
		// $content_actions = $this->buildContentActionUrls( $content_navigation );
		// But of course it *doesn't* because that method is literally _the_ only
		// private one in SkinTemplate...let's change that:
		$r = new ReflectionMethod( $skin, 'buildContentActionUrls' );
		$r->setAccessible( true );
		$content_actions = $r->invoke( $skin, $content_navigation );

		if ( !$title->inNamespace( NS_SPECIAL ) ) {
			// "What links here" isn't a part of default core content actions so we need
			// to add it there ourselves for all non-NS_SPECIAL namespaces
			$whatlinkshereTitle = SpecialPage::getTitleFor( 'Whatlinkshere', $title->getPrefixedDBkey() );
			$content_actions['whatlinkshere'] = [
				'class' => $title->isSpecial( 'Whatlinkshere' ) ? 'selected' : false,
				'text' => $skin->msg( 'whatlinkshere' )->plain(),
				'href' => $whatlinkshereTitle->getLocalURL(),
				'title' => Linker::titleAttrib( 't-whatlinkshere', 'withaccess' ),
				'accesskey' => Linker::accesskey( 't-whatlinkshere' )
			];

			// We don't need the watch (or unwatch) link in the "More actions" menu
			// as that link is already prominently exposed elsewhere in the UI
			if ( isset( $content_actions['watch'] ) ) {
				unset( $content_actions['watch'] );
			}
			if ( isset( $content_actions['unwatch'] ) ) {
				unset( $content_actions['unwatch'] );
			}
		} else {
			global $wgQuizID, $wgPictureGameID;

			/* show special page tab */
			if ( $title->isSpecial( 'QuizGameHome' ) && $request->getVal( 'questionGameAction' ) == 'editItem' ) {
				$quiz = SpecialPage::getTitleFor( 'QuizGameHome' );
				$content_actions[$title->getNamespaceKey()] = [
					'class' => 'selected',
					'text' => $skin->msg( 'nstab-special' )->plain(),
					'href' => $quiz->getFullURL( 'questionGameAction=renderPermalink&permalinkID=' . $wgQuizID ),
				];
			} else {
				$content_actions[$title->getNamespaceKey()] = [
					'class' => 'selected',
					'text' => $skin->msg( 'nstab-special' )->plain(),
					'href' => $request->getRequestURL(), // @bug 2457, 2510
				];
			}

			// "Edit" tab on Special:QuizGameHome for question game administrators
			if (
				$title->isSpecial( 'QuizGameHome' ) &&
				$user->isAllowed( 'quizadmin' ) &&
				$request->getVal( 'questionGameAction' ) != 'createForm' &&
				!empty( $wgQuizID )
			)
			{
				$quiz = SpecialPage::getTitleFor( 'QuizGameHome' );
				$content_actions['edit'] = [
					'class' => ( $request->getVal( 'questionGameAction' ) == 'editItem' ) ? 'selected' : false,
					'text' => $skin->msg( 'edit' )->plain(),
					'href' => $quiz->getFullURL( 'questionGameAction=editItem&quizGameId=' . $wgQuizID ), // @bug 2457, 2510
				];
			}

			// "Edit" tab on Special:PictureGameHome for picture game administrators
			if (
				$title->isSpecial( 'PictureGameHome' ) &&
				$user->isAllowed( 'picturegameadmin' ) &&
				$request->getVal( 'picGameAction' ) != 'startCreate' &&
				!empty( $wgPictureGameID )
			)
			{
				$picGame = SpecialPage::getTitleFor( 'PictureGameHome' );
				$content_actions['edit'] = [
					'class' => ( $request->getVal( 'picGameAction' ) == 'editPanel' ) ? 'selected' : false,
					'text' => $skin->msg( 'edit' )->plain(),
					'href' => $picGame->getFullURL( 'picGameAction=editPanel&id=' . $wgPictureGameID ), // @bug 2457, 2510
				];
			}
		}

		return $content_actions;
	}

	/**
	 * Gets the links for the action bar (edit, talk etc.)
	 *
	 * @return array
	 */
	function getActionBarLinks() {
		$left = array(
			$this->skin->getTitle()->getNamespaceKey(),
			'edit', 'talk', 'viewsource', 'addsection', 'history'
		);
		$actions = $this->buildActionBar();
		$moreLinks = [];

		foreach ( $actions as $action => $value ) {
			if ( in_array( $action, $left ) ) {
				$leftLinks[$action] = $value;
			} else {
				$moreLinks[$action] = $value;
			}
		}

		return array( $leftLinks, $moreLinks );
	}

	/**
	 * Generates the actual action bar - watch/unwatch links for logged-in users,
	 * "More actions" menu that has some other tools (WhatLinksHere special page etc.)
	 *
	 * @return $output HTML for action bar
	 */
	function actionBar() {
		$title = $this->skin->getTitle();
		$full_title = Title::makeTitle( $title->getNamespace(), $title->getText() );

		$output = '<div id="action-bar" class="noprint">';
		// Watch/unwatch link for registered users on namespaces that can be
		// watched (i.e. everything but the Special: namespace)
		if ( $this->skin->getUser()->isLoggedIn() && $title->getNamespace() != NS_SPECIAL ) {
			$output .= '<div id="article-controls">
				<span class="mw-skin-cali-watchplus">+</span>';

			// In 1.16, all we needed was the ID for AJAX page watching to work
			// In 1.18, we need the class *and* the title...w/o the title, the
			// new, jQuery-ified version of the AJAX page watching code dies
			// if the title attribute is not present
			if ( !$this->skin->getUser()->isWatched( $title ) ) {
				$output .= Linker::link(
					$full_title,
					wfMessage( 'watch' )->plain(),
					array(
						'id' => 'ca-watch',
						'class' => 'mw-watchlink',
						'title' => Linker::titleAttrib( 'ca-watch', 'withaccess' ),
						'accesskey' => Linker::accesskey( 'ca-watch' )
					),
					array( 'action' => 'watch' )
				);
			} else {
				$output .= Linker::link(
					$full_title,
					wfMessage( 'unwatch' )->plain(),
					array(
						'id' => 'ca-unwatch',
						'class' => 'mw-watchlink',
						'title' => Linker::titleAttrib( 'ca-unwatch', 'withaccess' ),
						'accesskey' => Linker::accesskey( 'ca-unwatch' )
					),
					array( 'action' => 'unwatch' )
				);
			}
			$output .= '</div>';
		}

		$output .= '<div id="article-tabs">';

		list( $leftLinks, $moreLinks ) = $this->getActionBarLinks();

		foreach ( $leftLinks as $key => $val ) {
			// @todo FIXME: this code deserves to burn in hell
			$output .= '<a href="' . htmlspecialchars( $val['href'] ) . '" class="mw-skin-cali-actiontab ' .
				( ( strpos( $val['class'], 'selected' ) === 0 ) ? 'tab-on' : 'tab-off' ) .
				( preg_match( '/new/i', $val['class'] ) ? ' tab-new' : '' ) . '"' .
				( isset( $val['title'] ) ? ' title="' . htmlspecialchars( $val['title'] ) . '"' : '' ) .
				( isset( $val['accesskey'] ) ? ' accesskey="' . htmlspecialchars( $val['accesskey'] ) . '"' : '' ) .
				( isset( $val['id'] ) ? ' id="' . htmlspecialchars( $val['id'] ) . '"' : '' ) .
				' rel="nofollow">
				<span>' . ucfirst( $val['text'] ) . '</span>
			</a>';
		}

		if ( count( $moreLinks ) > 0 ) {
			$output .= '<div class="mw-skin-cali-actiontab more-tab tab-off" id="more-tab">
				<span>' . wfMessage( 'cali-more-actions' )->plain() . '</span>';

			$output .= '<div class="article-more-actions" id="article-more-container" style="display:none">';

			$more_links_count = 1;

			foreach ( $moreLinks as $key => $val ) {
				if ( count( $moreLinks ) == $more_links_count ) {
					$border_fix = ' class="border-fix"';
				} else {
					$border_fix = '';
				}

				$output .= '<a href="' . htmlspecialchars( $val['href'] ) . '"' .
					( isset( $val['id'] ) ? ' id="' . htmlspecialchars( $val['id'] ) . '"' : '' ) .
					"{$border_fix} rel=\"nofollow\">" .
					ucfirst( $val['text'] ) .
				'</a>';

				$more_links_count++;
			}

			$output .= '</div>
			</div>';
		}

		$output .= '<div class="cleared"></div>
			</div>
		</div>';

		return $output;
	}

	/**
	 * Returns the footer for a page
	 *
	 * @return $footer The generated footer, including recent editors
	 */
	function footer() {
		global $wgUploadPath;

		$titleObj = $this->getSkin()->getTitle();
		$pageTitleId = $titleObj->getArticleID();
		$main_page = Title::newMainPage();

		$footerShow = array( NS_MAIN, NS_FILE );
		if ( defined( 'NS_VIDEO' ) ) {
			$footerShow[] = NS_VIDEO;
		}
		$footer = '';

		$services = MediaWikiServices::getInstance();
		$cache = $services->getMainWANObjectCache();
		$linkRenderer = $services->getLinkRenderer();

		// Show the list of recent editors and their avatars if the page is in
		// one of the allowed namespaces and it is not the main page
		if (
			in_array( $titleObj->getNamespace(), $footerShow ) &&
			( $pageTitleId != $main_page->getArticleID() )
		)
		{
			$key = $cache->makeKey( 'recenteditors', 'list', $pageTitleId );
			$data = $cache->get( $key );
			$editors = array();
			if ( !$data ) {
				wfDebug( __METHOD__ . ": Loading recent editors for page {$pageTitleId} from DB...\n" );
				$dbw = wfGetDB( DB_PRIMARY );

				$res = $dbw->select(
					[ 'revision_actor_temp', 'revision', 'actor' ],
					[ 'DISTINCT revactor_actor' ],
					[
						'revactor_page' => $pageTitleId,
						'actor_user IS NOT NULL',
						"actor_name <> 'MediaWiki default'"
					],
					__METHOD__,
					[ 'ORDER BY' => 'actor_name ASC', 'LIMIT' => 8 ],
					[
						'actor' => [ 'JOIN', 'actor_id = revactor_actor' ],
						'revision_actor_temp' => [ 'JOIN', 'revactor_rev = rev_id' ]
					]
				);

				foreach ( $res as $row ) {
					// Prevent blocked users from appearing
					$user = User::newFromActorId( $row->revactor_actor );
					if ( !$user->getBlock() ) {
						$editors[] = [
							'user_id' => $user->getId(),
							'user_name' => $user->getName()
						];
					}
				}

				// Cache for five minutes
				$cache->set( $key, $editors, 60 * 5 );
			} else {
				wfDebug( __METHOD__ . ": Loading recent editors for page {$pageTitleId} from cache...\n" );
				$editors = $data;
			}

			$x = 1;
			$per_row = 4;

			if ( count( $editors ) > 0 ) {
				$footer .= '<div id="footer-container" class="noprint">
					<div id="footer-actions">
						<h2>' . wfMessage( 'cali-contribute' )->plain() . '</h2>'
							. wfMessage( 'cali-pages-can-be-edited' )->parse() .
							Linker::link(
								$title,
								wfMessage( 'cali-editthispage' )->plain(),
								array(
									'class' => 'edit-action',
									'title' => Linker::titleAttrib( 'ca-edit', 'withaccess' ),
									'accesskey' => Linker::accesskey( 'ca-edit' )
								),
								array( 'action' => 'edit' )
							) .
							Linker::link(
								$title->getTalkPage(),
								wfMessage( 'cali-talkpage' )->plain(),
								array(
									'class' => 'discuss-action',
									'title' => Linker::titleAttrib( 'ca-talk', 'withaccess' ),
									'accesskey' => Linker::accesskey( 'ca-talk' )
								)
							) .
							Linker::link(
								$title,
								wfMessage( 'cali-pagehistory' )->plain(),
								array(
									'rel' => 'archives',
									'class' => 'page-history-action',
									'title' => Linker::titleAttrib( 'ca-history', 'withaccess' ),
									'accesskey' => Linker::accesskey( 'ca-history' )
								),
								array( 'action' => 'history' )
							);
				$footer .= '</div>';

				// Only load the page editors' avatars if wAvatar class exists and $wgUserLevels is an array
				global $wgUserLevels;
				if ( class_exists( 'wAvatar' ) && is_array( $wgUserLevels ) ) {
					$footer .= '<div id="footer-contributors">
						<h2>' . wfMessage( 'cali-recent-contributors' )->plain() . '</h2>'
						. wfMessage( 'cali-recent-contributors-info' )->plain() . '<br /><br />';

					foreach ( $editors as $editor ) {
						$avatar = new wAvatar( $editor['user_id'], 'm' );
						$user_title = Title::makeTitle( NS_USER, $editor['user_name'] );

						$footer .= '<a href="' . htmlspecialchars( $user_title->getFullURL() ) . '" rel="nofollow">';
						$footer .= $avatar->getAvatarURL( array(
							'alt' => htmlspecialchars( $editor['user_name'] ),
							'title' => htmlspecialchars( $editor['user_name'] )
						) );
						$footer .= '</a>';

						if ( $x == count( $editors ) || $x != 1 && $x % $per_row == 0 ) {
							$footer .= '<br />';
						}

						$x++;
					}

					$footer .= '</div>';
				}

				$footer .= '</div>';
			}
		}
		$footer .= '<footer id="footer-bottom" class="noprint">';
		foreach ( $this->getFooterLinks() as $category => $links ) {
			foreach ( $links as $link ) {
				$footer .= $this->get( $link );
				$footer .= "\n";
				if ( $link === 'copyright' ) {
					$footer .= '<br />';
				}
			}
		}
		$footer .= '<br />';
		foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) {
			$footer .= '<div id="' . Sanitizer::escapeIdForAttribute( "f-{$blockName}ico" ) . '" class="footer-icons">';
			foreach ( $footerIcons as $icon ) {
				$footer .= $this->getSkin()->makeFooterIcon( $icon );
			}
			$footer .= '</div>';
		}
		$footer .= "\n\t</footer>\n";

		return $footer;
	}

	/**
	 * Cheap ripoff from /skins/Games/Games.skin.php on 2 July 2013 with only
	 * one minor change for Cali: the addition of the wrapper div
	 * (.bottom-left-nav-container).
	 */
	function getInterlanguageLinksBox() {
		global $wgContLang, $wgHideInterlanguageLinks, $wgOut;

		$output = '';

		# Language links
		$language_urls = array();

		if ( !$wgHideInterlanguageLinks ) {
			foreach ( $wgOut->getLanguageLinks() as $l ) {
				$tmp = explode( ':', $l, 2 );
				$class = 'interwiki-' . $tmp[0];
				unset( $tmp );
				$nt = Title::newFromText( $l );
				if ( $nt ) {
					$langName = Language::fetchLanguageName(
						$nt->getInterwiki(),
						$wgContLang->getCode()
					);
					$language_urls[] = array(
						'href' => $nt->getFullURL(),
						'text' => ( $langName != '' ? $langName : $l ),
						'class' => $class
					);
				}
			}
		}

		if ( count( $language_urls ) ) {
			$output = '<div class="bottom-left-nav-container" id="cali-langlinks">';
			$output .= '<h2>' . wfMessage( 'otherlanguages' )->plain() . '</h2>';
			$output .= '<div class="interlanguage-links">' . "\n" . '<ul>' . "\n";
			foreach ( $language_urls as $langlink ) {
				$output .= '<li class="' . htmlspecialchars( $langlink['class'] ) . '">
					<a href="' . htmlspecialchars( $langlink['href'] ) . '">' .
						$langlink['text'] . '</a>
				</li>';
			}
			$output .= "</ul>\n</div></div>";
		}

		return $output;
	}
	
	/**
	 * Get the current user's avatar image.
	 *
	 * @param int $width 20 or 40, used for the img ID and class attributes
	 * @return string Valid <\img\> tag suitable for output
	 */
	private function getAvatar( $width ) {
		$skin = $this->getSkin();

		// Default avatar is what we start with
		$avatarImage = Html::element(
			'img',
			[
				'class' => 'userIcon' . (int)$width,
				'width' => (int)$width,
				'height' => (int)$width,
				'src' => htmlspecialchars( '/w/skins/Cali/resources/img/user-icon-default.svg' ),
				'alt' => ''
			]
		);

		if ( class_exists( 'wAvatar' ) ) {
			// SocialProfile is installed
			$avatar = new wAvatar( $skin->getUser()->getId(), 'l' );
			$avatarImage = $avatar->getAvatarURL( [
				'width' => (int)$width,
				'height' => (int)$width,
				'class' => 'userIcon' . (int)$width . ' socialprofile-avatar'
			] );
		}

		return $avatarImage;
	}
	
	/**
	 * @inheritDoc
	 */
	public function makeLink( $key, $item, $options = [] ) {
		$html = parent::makeLink( $key, $item, $options );
		return $html;
	}

	/**
	 * @inheritDoc
	 */
	public function makeListItem( $key, $item, $options = [] ) {

		// We don't use this, prevent it from popping up in HTML output
		unset( $item['redundant'] );

		return parent::makeListItem( $key, $item, $options );
	}
	
} // end of class
