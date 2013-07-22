<?php if (!defined('ABS_PATH')) exit('ABS_PATH is not loaded. Direct access is not allowed.');
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
     * Model database for Voting tables
     *
     * @package OSClass
     * @subpackage Model
     * @since 3.0
     */
    class ModelVoting extends DAO
    {
        /**
         * It references to self object: ModelVoting.
         * It is used as a singleton
         *
         * @access private
         * @since 3.0
         * @var ModelVoting
         */
        private static $instance ;

        /**
         * It creates a new ModelVoting object class ir if it has been created
         * before, it return the previous object
         *
         * @access public
         * @since 3.0
         * @return ModelVoting
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
         * Return table name voting item
         * @return string
         */
        public function getTable_Item()
        {
            return DB_TABLE_PREFIX.'t_voting_item';
        }

        /**
         * Return table name voting user
         * @return string
         */
        public function getTable_User()
        {
            return DB_TABLE_PREFIX.'t_voting_user';
        }

        /**
         * Import sql file
         * @param type $file
         */
        public function import($file)
        {
            $path = osc_plugin_resource($file) ;
            $sql = file_get_contents($path);

            if(! $this->dao->importSQL($sql) ){
                throw new Exception( "Error importSQL::ModelVoting<br>".$file ) ;
            }
        }

        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query("DELETE FROM ".DB_TABLE_PREFIX."t_plugin_category WHERE s_plugin_name = 'voting'" );
            $this->dao->query("DROP TABLE ".DB_TABLE_PREFIX."t_voting_item");
            $this->dao->query("DROP TABLE ".DB_TABLE_PREFIX."t_voting_user");
        }

        // item related --------------------------------------------------------

        /**
         * Insert Item rating
         *
         * @param type $itemId
         * @param type $userId
         * @param type $iVote
         * @param type $hash
         * @return type
         */
        function insertItemVote($itemId, $userId, $iVote, $hash)
        {
            $aSet = array(
                'fk_i_item_id'  => (int)$itemId,
                'i_vote'        => (int)$iVote,
                's_hash'        => is_null($hash) ? "" : "$hash"
            );
            if($userId != 'NULL' && is_numeric($userId) ) {
                $aSet['fk_i_user_id']  = $userId;
            }

            return $this->dao->insert($this->getTable_Item(), $aSet);
        }
        /**
         * Return an average of ratings given an item id
         *
         * @param type $id
         * @return type
         */
        function getItemAvgRating($id)
        {
            if(is_numeric($id)) {
                $this->dao->select('format(avg(i_vote),1) as vote');
                $this->dao->from( $this->getTable_Item());
                $this->dao->where('fk_i_item_id', (int)$id );

                $result = $this->dao->get();
                if( !$result ) {
                    return array() ;
                }

                return $result->row();
            } else {
                return array('vote' => 0);
            }
        }

        /**
         * Return the number of votes given an item id
         *
         * @param type $id
         * @return type
         */
        function getItemNumberOfVotes($id)
        {
            if(is_numeric($id)) {
                $this->dao->select('count(*) as total');
                $this->dao->from( $this->getTable_Item());
                $this->dao->where('fk_i_item_id', (int)$id );

                $result = $this->dao->get();
                if( !$result ) {
                    return array() ;
                }

                return $result->row();
            } else {
                return array('total' => 0);
            }
        }

        /**
         * Return rating given an item id and hash
         *
         * @param type $itemId
         * @param type $hash
         * @return type
         */
        function getItemIsRated($itemId, $hash, $userId = null)
        {
            if( is_numeric($itemId) && ( $userId == null || is_numeric($userId) ) ) {
                $this->dao->select('i_vote');
                $this->dao->from( $this->getTable_Item());
                $this->dao->where('fk_i_item_id', (int)$itemId );
                if( $userId == null) {
                    $this->dao->where('fk_i_user_id IS NULL');
                } else {
                    $this->dao->where('fk_i_user_id', (int)$userId);
                }

                $this->dao->where('s_hash'      , (string)$hash );

                $result = $this->dao->get();
                if( !$result ) {
                    return array() ;
                }

                return $result->row();
            } else {
                return array();
            }
        }

        /**
         * Return an array of items ordered by avg_votes
         *
         * @param type $category_id
         * @param type $order
         * @param type $num
         * @return type
         */
        function getItemRatings($category_id = null, $order = 'desc', $num = 5)
        {
            $sql  = 'SELECT fk_i_item_id as item_id, format(avg(i_vote),1) as avg_vote, count(*) as num_votes, '.DB_TABLE_PREFIX.'t_item.fk_i_category_id as category_id ';
            if(!is_null($category_id)) {
                $sql .= ', '.DB_TABLE_PREFIX.'t_category.fk_i_parent_id as parent_category_id ';
            }
            $sql .= 'FROM '.DB_TABLE_PREFIX.'t_voting_item ';
            $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_item ON '.DB_TABLE_PREFIX.'t_item.pk_i_id = '.DB_TABLE_PREFIX.'t_voting_item.fk_i_item_id ';
            $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_category ON '.DB_TABLE_PREFIX.'t_category.pk_i_id = '.DB_TABLE_PREFIX.'t_item.fk_i_category_id ';
            if(!is_null($category_id)) {
                $sql .= 'WHERE '.DB_TABLE_PREFIX.'t_item.fk_i_category_id = '.$category_id.' ';
                $sql .= 'OR '.DB_TABLE_PREFIX.'t_category.fk_i_parent_id = '.$category_id.' ';
                $sql .= ' AND ';
            }else{
                $sql .= 'WHERE ';
            }
            $sql .= ''.DB_TABLE_PREFIX.'t_item.b_active = 1 ';
            $sql .= 'AND '.DB_TABLE_PREFIX.'t_item.b_enabled = 1 ';
            $sql .= 'AND '.DB_TABLE_PREFIX.'t_item.b_spam = 0 ';
            $sql .= 'AND ('.DB_TABLE_PREFIX.'t_item.b_premium = 1 || '.DB_TABLE_PREFIX.'t_category.i_expiration_days = 0 ||DATEDIFF(\''.date('Y-m-d H:i:s').'\','.DB_TABLE_PREFIX.'t_item.dt_pub_date) < '.DB_TABLE_PREFIX.'t_category.i_expiration_days) ';
            $sql .= 'AND '.DB_TABLE_PREFIX.'t_category.b_enabled = 1 ';
            $sql .= 'GROUP BY item_id ORDER BY avg_vote '.$order.', num_votes '.$order.' LIMIT 0, '.$num;

            $result = $this->dao->query($sql);
            if( !$result ) {
                return array() ;
            }

            return $result->result();
        }

        /**
         * Delete table entries related to an item id
         *
         * @param type $itemId
         * @return type
         */
        function deleteItem($itemId)
        {
            if(is_numeric($itemId)) {
                return $this->dao->delete($this->getTable_Item(), 'fk_i_item_id = '.$itemId);
            }
            return false;
        }

        // user related --------------------------------------------------------

        /**
         * Insert a user rating
         *
         * @param type $votedUserId
         * @param type $userId
         * @param type $iVote
         * @return type
         */
        function insertUserVote($votedUserId, $userId, $iVote)
        {
            $aSet = array(
                'i_user_voted'  => (int)$votedUserId,
                'i_user_voter'  => (int)$userId,
                'i_vote'        => (int)$iVote
            );
            return $this->dao->insert($this->getTable_User(), $aSet);
        }

         /**
         * Return an average of ratings given an user id
         *
         * @param type $id
         * @return type
         */
        function getUserAvgRating($id)
        {
            if(is_numeric($id)) {
                $this->dao->select('format(avg(i_vote),1) as vote');
                $this->dao->from( $this->getTable_User());
                $this->dao->where('i_user_voted', (int)$id );

                $result = $this->dao->get();
                if( !$result ) {
                    return array() ;
                }

                return $result->row();
            } else {
                return array('vote' => 0);
            }
        }

        /**
         * Return the number of votes given an item id
         *
         * @param type $id
         * @return type
         */
        function getUserNumberOfVotes($id)
        {
            if(is_numeric($id)) {
                $this->dao->select('count(*) as total');
                $this->dao->from( $this->getTable_User());
                $this->dao->where('i_user_voted', (int)$id );

                $result = $this->dao->get();
                if( !$result ) {
                    return array() ;
                }

                return $result->row();
            } else {
                return array('total' => 0);
            }
        }

        /**
         * Return user rating given : userid for voting and voted
         *
         * @param type $userVotedId
         * @param type $userId
         * @return type
         */
        function getUserIsRated($userVotedId, $userId)
        {
            if( is_numeric($userVotedId) && is_numeric($userId) ) {
                $this->dao->select('i_vote');
                $this->dao->from( $this->getTable_User());
                $this->dao->where('i_user_voted', (int)$userVotedId );
                $this->dao->where('i_user_voter', (int)$userId );

                $result = $this->dao->get();
                if( !$result ) {
                    return array() ;
                }

                return $result->row();
            } else {
                return array();
            }
        }

        /**
         * Return an array of user's ordered by avg_vote.
         *
         * @param type $order
         * @param type $num
         * @return type
         */
        function getUserRatings($order = 'desc', $num = 5)
        {
            $sql  = 'SELECT i_user_voted as user_id, format(avg(i_vote),1) as avg_vote, count(*) as num_votes ';
            $sql .= 'FROM '.DB_TABLE_PREFIX.'t_voting_user ';
            $sql .= 'LEFT JOIN '.DB_TABLE_PREFIX.'t_user ON '.DB_TABLE_PREFIX.'t_user.pk_i_id = '.DB_TABLE_PREFIX.'t_voting_user.i_user_voted ';
            $sql .= 'WHERE ';
            $sql .= ''.DB_TABLE_PREFIX.'t_user.b_active = 1 ';
            $sql .= 'AND '.DB_TABLE_PREFIX.'t_user.b_enabled = 1 ';
            $sql .= 'GROUP BY user_id ORDER BY avg_vote '.$order.', num_votes '.$order.' LIMIT 0, '.$num;

            $result = $this->dao->query($sql);
            if( !$result ) {
                return array() ;
            }

            return $result->result();
        }

        /**
         * Delete table entries related to this user id
         *
         * @param type $userId
         * @return type
         */
        function deleteUser($userId)
        {
            if(is_numeric($userId)) {
                $aux  = $this->dao->delete($this->getTable_User(), 'i_user_voted = '.$userId);
                $aux2 = $this->dao->delete($this->getTable_User(), 'i_user_voter = '.$userId);
                return ($aux && $aux2);
            }
            return false;
        }
    }
?>