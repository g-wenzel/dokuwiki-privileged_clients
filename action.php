<?php
/**
 * Remote Host Group Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Gregor Wenzel <gregor.wenzel@charite.de>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'action.php');

class action_plugin_remotehostgroup extends DokuWiki_Action_Plugin {
    public function __construct() {

        $this->permitted_domain = $this->getConf('permitted_domain');
    }
    /**
     * Register event handlers
     */
    public function register(Doku_Event_Handler $controller) {
		$controller->register_hook('AUTH_ACL_CHECK', 'BEFORE', $this, 'start');
    }

    function start(&$event, $param) {
		// get remote hostname
        $remote_host=$_SERVER['REMOTE_HOST'];
		// read config file or create
        if (str_ends_with($remote_host,$this->permitted_domain)) {
            $filecontent = @file(DOKU_CONF.'remote_host_group.conf', FILE_SKIP_EMPTY_LINES);
            if ($filecontent === false) { $filecontent = array(); }
            $remote_host=str_ireplace(".".$this->permitted_domain,"",$remote_host);
            // check current hostname against each known hostname
            foreach ($filecontent as $line) {
                // seperate network and group and trim spaces
                list($hostname,$group) = explode(';', $line);
                $hostname = rtrim($hostname);
                $group = rtrim($group);
                // check if host is in list
                if (strcasecmp($hostname,$remote_host) == 0) {
                    // add group to list
                    $event->data['groups'][] = $group;
                }
                
            }
        }
    }
}