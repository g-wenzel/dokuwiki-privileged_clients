<?php
/**
 * remotehostgroup Plugin
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Sascha Bendix <sascha.bendix@localroot.de>
 * @author     Marcel Pennewiss <opensource@pennewiss.de>
 * @author     Peter Grosse <pegro@fem-net.de>
 * @author     Jonas Licht <jonas.licht@fem.tu-ilmenau.de>
 */

$invalid_hostname_regex = '/[^a-zA-Z\d\-_]/'; //hostname mus only consist of alphanumeric and dash/underscore

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_PLUGIN.'admin.php');

class admin_plugin_remotehostgroup extends DokuWiki_Admin_Plugin {

    /**
     * This functionality should be available only to administrator
     */
    function forAdminOnly() {
        return true;
    }

    /**
     * Handles user request
     */
    function handle() {
        if (isset($_REQUEST['remote_hostname']) && ($_REQUEST['remote_hostname'] != '')
	    && isset($_REQUEST['group']) && ($_REQUEST['group'] != '')) {
            // remote_hostname and group should be added to the list of trusted computers
            // check input
	    $config_row = $_REQUEST['remote_hostname'].';'.$_REQUEST['group']."\n";
            $hostname_is_invalid = preg_match($invalid_hostname_regex,$_REQUEST['remote_hostname']);
            if ($hostname_is_invalid == 0)  {
                $filecontent = @file(DOKU_CONF.'remote_host_group.conf', FILE_SKIP_EMPTY_LINES);
                if ($filecontent && (sizeof($filecontent) > 0)) {
                    if (in_array($config_row, $filecontent)) {
                        msg($this->getLang('already'), -1);
                        return;
                    }
                }
                io_saveFile(DOKU_CONF.'remote_host_group.conf', $config_row, true);
            } else {
                msg("Input generates illegal characters.", -1);
            }
        } elseif (isset($_REQUEST['delete']) && is_array($_REQUEST['delete']) && (sizeof($_REQUEST['delete']) > 0)) {
            // delete hostnaame-mapping from the list
	    if (!io_deleteFromFile(DOKU_CONF.'remote_host_group.conf', key($_REQUEST['delete'])."\n")) {
	    	msg($this->getLang('failed'), -1);
	    }
        } elseif (isset($_REQUEST['clear'])) {
            if (file_exists($conf['cachedir'].'/remote_host_group')) {
                @unlink($conf['cachedir'].'/remote_host_group');
            }
        }
    }

    /**
     * Shows edit form
     */
    function html() {
        global $conf;

        print $this->locale_xhtml('intro');

        print $this->locale_xhtml('list');
        ptln("<div class=\"level2\">");
        ptln("<form action=\"\" method=\"post\">");
        formSecurityToken();
        $hosts = @file(DOKU_CONF.'remote_host_group.conf', FILE_SKIP_EMPTY_LINES);
        if ($hosts && (sizeof($hosts) > 0)) {
            ptln("<table class=\"inline\">");
            ptln("<colgroup width=\"250\"></colgroup>");
            ptln("<colgroup width=\"150\"></colgroup>");
            ptln("<thead>");
            ptln("<tr>");
            ptln("<th>".$this->getLang('remote_hostname')."</th>");
            ptln("<th>".$this->getLang('group')."</th>");
            ptln("<th>".$this->getLang('delete')."</th>");
            ptln("</tr>");
            ptln("</thead>");
            ptln("<tbody>");
            foreach ($hosts as $host) {
                $host = rtrim($host);
		list($host, $group) = explode(';', $host);
                ptln("<tr>");
                ptln("<td>".rtrim($host)."</td>");
                ptln("<td>".rtrim($group)."</td>");
                ptln("<td>");
                ptln("<input type=\"submit\" name=\"delete[".$host.";".$group."]\" value=\"".$this->getLang('delete')."\" class=\"button\">");
                ptln("</td>");
                ptln("</tr>");
            }
            ptln("</tbody>");
            ptln("</table>");
        } else {
            ptln("<div class=\"fn\">".$this->getLang('nohosts')."</div>");
        }
        ptln("</form>");
        ptln("</div>");

        print $this->locale_xhtml('add');
        ptln("<div class=\"level2\">");
        ptln("<form action=\"\" method=\"post\">");
        formSecurityToken();
        ptln("<label for=\"host__add\">".$this->getLang('remote_hostname').":</label>");
        ptln("<input id=\"host__add\" name=\"remote_hostname\" type=\"text\" maxlength=\"44\" class=\"edit\">");
        ptln("<label for=\"group__add\">".$this->getLang('group').":</label>");
        ptln("<input id=\"group__add\" name=\"group\" type=\"text\" maxlength=\"64\" class=\"edit\">");
        ptln("<input type=\"submit\" value=\"".$this->getLang('add')."\" class=\"button\">");
        ptln("</form>");
        ptln("</div>");

        if (file_exists($conf['cachedir'].'/remote_host_group')) {
            @unlink($conf['cachedir'].'/remote_host_group');
        }
    }
}
