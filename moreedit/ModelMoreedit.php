<?php
    /*
     *      OSCLass – software for creating and publishing online classified
     *                           advertising platforms
     *
     *                        Copyright (C) 2010 OSCLASS
     *
     *       This program is free software: you can redistribute it and/or
     *     modify it under the terms of the GNU Affero General Public License
     *     as published by the Free Software Foundation, either version 3 of
     *            the License, or (at your option) any later version.
     *
     *     This program is distributed in the hope that it will be useful, but
     *         WITHOUT ANY WARRANTY; without even the implied warranty of
     *        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *             GNU Affero General Public License for more details.
     *
     *      You should have received a copy of the GNU Affero General Public
     * License along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

    /**
     * Model database for Moreedit plugin
     *
     * @package OSClass
     * @subpackage Model
     * @since 3.0
     */
    class ModelMoreedit extends DAO
    {
        /**
         * It references to self object: ModelMoreedit.
         * It is used as a singleton
         *
         * @access private
         * @since 3.0
         * @var ModelCars
         */
        private static $instance ;

        /**
         * It creates a new ModelMoreedit object class ir if it has been created
         * before, it return the previous object
         *
         * @access public
         * @since 3.0
         * @return ModelMoreedit
         */
        public static function newInstance()
        {
            if( !self::$instance instanceof self ) {
                self::$instance = new self ;
            }
            return self::$instance ;
        }

        /**
         * Construct
         */
        function __construct()
        {
            parent::__construct();
        }

        /**
         * Install plugin moreedit
         */
        public function install()
        {
            $array = array(
                's_internal_name'   => 'email_moreedit_notify_edit',
                'b_indelible'       => 1,
                'dt_pub_date'       => date('Y-m-d H:i:s')
            );
            $this->dao->insert(DB_TABLE_PREFIX.'t_pages', $array);
            $id = $this->dao->insertedId();

            $this->dao->insert(DB_TABLE_PREFIX.'t_pages_description', $array);

            $array_description = array(
                'fk_i_pages_id'     => $id,
                'fk_c_locale_code'  => osc_language(),
                's_title'           => '{WEB_TITLE} - Notification of ad: {ITEM_TITLE}',
                's_text'            => '<p>Hi Admin!</p>\r\n<p> </p>\r\n<p>We just published an item ({ITEM_TITLE}) on {WEB_TITLE} from user {USER_NAME} ( {ITEM_URL} ).</p>\r\n<p>Edit it here : {EDIT_LINK}</p>\r\n<p> </p>\r\n<p>Thanks</p>'
            );
        }

        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            Page::newInstance()->deleteByInternalName( 'email_moreedit_notify_edit' );
        }


        public function ads_per_week($user_id)
        {
            $sql = sprintf("SELECT COUNT(pk_i_id) as total FROM %st_item WHERE fk_i_user_id = %d AND TIMESTAMPDIFF(DAY,%st_item.dt_pub_date,'%s') < 7",DB_TABLE_PREFIX, $user_id, DB_TABLE_PREFIX, date('Y-m-d H:i:s'));
            $rs  = $this->dao->query($sql);
            $data   = $rs->row();
            if( !isset($data['total']) ) {
                return 0;
            }
            return $data['total'];
        }

        public function ads_per_week_email($email)
        {
            $sql = sprintf("SELECT COUNT(pk_i_id) as total FROM %st_item WHERE s_contact_email = '%s' AND TIMESTAMPDIFF(DAY,%st_item.dt_pub_date,'%s') < 7",DB_TABLE_PREFIX, $email, DB_TABLE_PREFIX, date('Y-m-d H:i:s'));
            $rs  = $this->dao->query($sql);
            $data   = $rs->row();
            if( !isset($data['total']) ) {
                return 0;
            }
            return $data['total'];
        }

        public function ads_per_month($user_id)
        {
            $sql = sprintf("SELECT COUNT(pk_i_id) as total FROM %st_item WHERE fk_i_user_id = %d AND TIMESTAMPDIFF(DAY,%st_item.dt_pub_date,'%s') < 30", DB_TABLE_PREFIX, $user_id, DB_TABLE_PREFIX, date('Y-m-d H:i:s'));
            $rs  = $this->dao->query($sql);
            $data   = $rs->row();
            if( !isset($data['total']) ) {
                return 0;
            }
            return $data['total'];
        }

        public function ads_per_month_email($email)
        {
            $sql = sprintf("SELECT COUNT(pk_i_id) as total FROM %st_item WHERE s_contact_email = '%s' AND TIMESTAMPDIFF(DAY,%st_item.dt_pub_date,'%s') < 30", DB_TABLE_PREFIX, $email, DB_TABLE_PREFIX, date('Y-m-d H:i:s'));
            $rs  = $this->dao->query($sql);
            $data   = $rs->row();
            if( !isset($data['total']) ) {
                return 0;
            }
            return $data['total'];
        }
    }
?>