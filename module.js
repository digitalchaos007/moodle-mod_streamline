/**
 * @package   mod_streamline
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @copyright 2012-2014 Blindside Networks Inc.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v2 or later
 */
M.mod_streamline = M.mod_streamline || {};

/**
 * This function is initialized from PHP
 * 
 * @param {Object}
 *            Y YUI instance
 */

M.mod_streamline.init_view = function(Y) {
    if (streamline.joining == 'true') {
        if (streamline.isadministrator == 'true' || streamline.ismoderator == 'true' || streamline.waitformoderator == 'false') {
            M.mod_streamline.joinURL();
        } else {

            var dataSource = new Y.DataSource.Get({
                source : M.cfg.wwwroot + "/mod/streamline/ping.php?"
            });

            var request = {
                request : "meetingid=" + streamline.meetingid + "&id=" + streamline.bigbluebuttonbnid,
                callback : {
                    success : function(e) {
                        if (e.data.status == 'true') {
                            M.mod_streamline.joinURL();
                        }
                    },
                    failure : function(e) {
                        console.debug(e.error.message);
                    }
                }
            };

            var id = dataSource.setInterval(10000, request);

        }
    }
};

M.mod_streamline.joinURL = function() {
    window.location = streamline.joinurl;
};

M.mod_streamline.viewend_CloseWindow = function() {
    window.close();
};
