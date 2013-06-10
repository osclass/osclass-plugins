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
     * Model database for payments classes
     *
     * @package OSClass
     * @subpackage Model
     * @since 3.0
     */
    class ModelPayment extends DAO
    {
        /**
         * It references to self object: ModelPayment.
         * It is used as a singleton
         *
         * @access private
         * @since 3.0
         * @var ModelPayment
         */
        private static $instance ;

        /**
         * It creates a new ModelPayment object class ir if it has been created
         * before, it return the previous object
         *
         * @access public
         * @since 3.0
         * @return ModelPayment
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

        public function getTable_log()
        {
            return DB_TABLE_PREFIX.'t_payments_log';
        }

        public function getTable_wallet()
        {
            return DB_TABLE_PREFIX.'t_payments_wallet';
        }

        public function getTable_premium()
        {
            return DB_TABLE_PREFIX.'t_payments_premium';
        }

        public function getTable_publish()
        {
            return DB_TABLE_PREFIX.'t_payments_publish';
        }

        public function getTable_prices()
        {
            return DB_TABLE_PREFIX.'t_payments_prices';
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
                throw new Exception( "Error importSQL::ModelPayment<br>".$file ) ;
            }
        }

        public function install() {

            $this->import('payment/struct.sql');

            osc_set_preference('version', '2.0', 'payment', 'INTEGER');
            osc_set_preference('default_premium_cost', '1.0', 'payment', 'STRING');
            osc_set_preference('allow_premium', '0', 'payment', 'BOOLEAN');
            osc_set_preference('default_publish_cost', '1.0', 'payment', 'STRING');
            osc_set_preference('pay_per_post', '0', 'payment', 'BOOLEAN');
            osc_set_preference('premium_days', '7', 'payment', 'INTEGER');
            osc_set_preference('currency', 'USD', 'payment', 'STRING');
            osc_set_preference('pack_price_1', '', 'payment', 'STRING');
            osc_set_preference('pack_price_2', '', 'payment', 'STRING');
            osc_set_preference('pack_price_3', '', 'payment', 'STRING');

            osc_set_preference('paypal_api_username', '', 'payment', 'STRING');
            osc_set_preference('paypal_api_password', '', 'payment', 'STRING');
            osc_set_preference('paypal_api_signature', '', 'payment', 'STRING');
            osc_set_preference('paypal_email', '', 'payment', 'STRING');
            osc_set_preference('paypal_standard', '1', 'payment', 'BOOLEAN');
            osc_set_preference('paypal_sandbox', '1', 'payment', 'BOOLEAN');
            osc_set_preference('paypal_enabled', '1', 'payment', 'BOOLEAN');

            osc_set_preference('blockchain_btc_address', '', 'payment', 'STRING');
            osc_set_preference('blockchain_enabled', '1', 'payment', 'BOOLEAN');

            $this->dao->select('pk_i_id') ;
            $this->dao->from(DB_TABLE_PREFIX.'t_item') ;
            $result = $this->dao->get();
            if($result) {
                $items  = $result->result();
                $date = date("Y-m-d H:i:s");
                foreach($items as $item) {
                    $this->createItem($item['pk_i_id'], 1, $date);
                }
            }

            $description[osc_language()]['s_title'] = '{WEB_TITLE} - Publish option for your ad: {ITEM_TITLE}';
            $description[osc_language()]['s_text'] = '<p>Hi {CONTACT_NAME}!</p><p>We just published your item ({ITEM_TITLE}) on {WEB_TITLE}.</p><p>{START_PUBLISH_FEE}</p><p>In order to make your ad available to anyone on {WEB_TITLE}, you should complete the process and pay the publish fee. You could do that on the following link: {PUBLISH_LINK}</p><p>{END_PUBLISH_FEE}</p><p>{START_PREMIUM_FEE}</p><p>You could make your ad premium and make it to appear on top result of the searches made on {WEB_TITLE}. You could do that on the following link: {PREMIUM_LINK}</p><p>{END_PREMIUM_FEE}</p><p>This is an automatic email, if you already did that, please ignore this email.</p><p>Thanks</p>';
            $res = Page::newInstance()->insert(
                array('s_internal_name' => 'email_payment', 'b_indelible' => '1'),
                $description
                );

        }

        public function premiumOff($id) {
            $this->dao->delete($this->getTable_premium(), array('fk_i_item_id' => $id));
        }

        public function deleteItem($id) {
            $this->premiumOff($id);
            $this->dao->delete($this->getTable_publish(), array('fk_i_item_id' => $id));
        }

        /**
         * Remove data and tables related to the plugin.
         */
        public function uninstall()
        {
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_premium()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_publish()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_wallet()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_prices()) ) ;
            $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_log()) ) ;

            $page = Page::newInstance()->findByInternalName('email_payment');
            Page::newInstance()->deleteByPrimaryKey($page['pk_i_id']);

            osc_delete_preference('version', 'payment');
            osc_delete_preference('default_premium_cost', 'payment');
            osc_delete_preference('allow_premium', 'payment');
            osc_delete_preference('default_publish_cost', 'payment');
            osc_delete_preference('pay_per_post', 'payment');
            osc_delete_preference('premium_days', 'payment');
            osc_delete_preference('currency', 'payment');
            osc_delete_preference('pack_price_1', 'payment');
            osc_delete_preference('pack_price_2', 'payment');
            osc_delete_preference('pack_price_3', 'payment');

            osc_delete_preference('paypal_api_username', 'payment');
            osc_delete_preference('paypal_api_password', 'payment');
            osc_delete_preference('paypal_api_signature', 'payment');
            osc_delete_preference('paypal_email', 'payment');
            osc_delete_preference('paypal_standard', 'payment');
            osc_delete_preference('paypal_sandbox', 'payment');
            osc_delete_preference('paypal_enabled', 'payment');

            osc_delete_preference('blockchain_btc_address', 'payment');
            osc_delete_preference('blockchain_enabled', 'payment');

        }

        public function versionUpdate() {
            $version = osc_get_preference('version', 'payment');
            if( $version < 200 ) {
                osc_set_preference('version', 200, 'payment', 'INTEGER');
                $this->dao->query(sprintf('ALTER TABLE %s ADD i_amount BIGINT(20) NULL AFTER f_amount', ModelPayment::newInstance()->getTable_log()));
                $this->dao->query(sprintf('ALTER TABLE %s ADD i_amount BIGINT(20) NULL AFTER f_amount', ModelPayment::newInstance()->getTable_wallet()));

                $this->dao->select('*') ;
                $this->dao->from($this->getTable_wallet());
                $result = $this->dao->get();
                if($result) {
                    $wallets = $result->result();
                    foreach($wallets as $w) {
                        $this->dao->update($this->getTable_wallet(), array('i_amount' => $w['f_amount']*1000000000000), array('fk_i_user_id' => $w['fk_i_user_id']));
                    }
                }

                $this->dao->select('*') ;
                $this->dao->from($this->getTable_log());
                $result = $this->dao->get();
                if($result) {
                    $logs = $result->result();
                    foreach($logs as $log) {
                        $this->dao->update($this->getTable_log(), array('i_amount' => $log['f_amount']*1000000000000), array('pk_i_id' => $log['pk_i_id']));
                    }
                }


                osc_reset_preferences();
            }
        }

        public function getPaymentByCode($code, $source) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_log());
            $this->dao->where('s_code', $code);
            $this->dao->where('s_source', $source);
            $result = $this->dao->get();
            if($result) {
                return $result->row();
            }
            return false;
        }

        public function getPayment($paymentId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_log());
            $this->dao->where('pk_i_id', $paymentId);
            $result = $this->dao->get();
            if($result) {
                return $result->row();
            }
            return false;
        }

        public function getPublishData($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_publish());
            $this->dao->where('fk_i_item_id', $itemId);
            $result = $this->dao->get();
            if($result) {
                return $result->row();
            }
            return false;
        }

        public function getPremiumData($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_premium());
            $this->dao->where('fk_i_item_id', $itemId);
            $result = $this->dao->get();
            if($result) {
                return $result->row();
            }
            return false;
        }

        public function createItem($itemId, $paid = 0, $date = NULL, $paypal = NULL) {
            if($date==NULL) { $date = date("Y-m-d H:i:s"); };
            $this->dao->insert($this->getTable_publish(), array('fk_i_item_id' => $itemId, 'dt_date' => $date, 'b_paid' => $paid, 'fk_i_payment_id' => $paypal));
        }

        public function getPublishPrice($categoryId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_prices());
            $this->dao->where('fk_i_category_id', $categoryId);
            $result = $this->dao->get();
            if($result) {
                $cat = $result->row();
                return $cat["f_publish_cost"];
            }
            return osc_get_preference('default_publish_cost', 'payment');
        }

        public function getPremiumPrice($categoryId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_prices());
            $this->dao->where('fk_i_category_id', $categoryId);
            $result = $this->dao->get();
            if($result) {
                $cat = $result->row();
                return $cat["f_premium_cost"];
            }
            return osc_get_preference('default_premium_cost', 'payment');
        }

        public function getWallet($userId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_wallet());
            $this->dao->where('fk_i_user_id', $userId);
            $result = $this->dao->get();
            if($result) {
                $row = $result->row();
                $row['formatted_amount'] = (isset($row['i_amount'])?$row['i_amount']:0)/1000000000000;
                return $row;
            }
            return false;
        }

        public function getCategoriesPrices() {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_prices());
            $result = $this->dao->get();
            if($result) {
                return $result->result();
            }
            return array();
        }

        public function publishFeeIsPaid($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_publish());
            $this->dao->where('fk_i_item_id', $itemId);
            $result = $this->dao->get();
            $row = $result->row();
            if($row) {
                if($row['b_paid']==1) {
                    return true;
                } else {
                    return false;
                }
            }
            return false;
        }

        public function premiumFeeIsPaid($itemId) {
            $this->dao->select('*') ;
            $this->dao->from($this->getTable_premium());
            $this->dao->where('fk_i_item_id', $itemId);
            $this->dao->where(sprintf("TIMESTAMPDIFF(DAY,dt_date,'%s') < %d", date('Y-m-d H:i:s'), osc_get_preference("premium_days", "payment")));
            $result = $this->dao->get();
            $row = $result->row();
            if(isset($row['dt_date'])) {
                return true;
            }
            return false;
        }


        public function purgeExpired() {
            $this->dao->select("fk_i_item_id");
            $this->dao->from($this->getTable_premium());
            $this->dao->where(sprintf("TIMESTAMPDIFF(DAY,dt_date,'%s') >= %d", date('Y-m-d H:i:s'), osc_get_preference("premium_days", "payment")));
            $result = $this->dao->get();
            if($result) {
                $items = $result->result();
                $mItem = new ItemActions(false);
                foreach($items as $item) {
                    $mItem->premium($item['fk_i_item_id'], false);
                    $this->premiumOff($item['fk_i_item_id']);
                };
            };
        }


        /**
         * Create a record on the DB for the paypal transaction
         *
         * @param string $concept
         * @param string $code
         * @param float $amount
         * @param string $currency
         * @param string $email
         * @param integer $user
         * @param integer $item
         * @param string $product_type (publish fee, premium, pack and which category)
         * @param string $source
         * @return integer $last_id
         */
        public function saveLog($concept, $code, $amount, $currency, $email, $user, $item, $product_type, $source) {

            $this->dao->insert($this->getTable_log(), array(
                's_concept' => $concept,
                'dt_date' => date("Y-m-d H:i:s"),
                's_code' => $code,
                'i_amount' => $amount*1000000000000,
                's_currency_code' => $currency,
                's_email' => $email,
                'fk_i_user_id' => $user,
                'fk_i_item_id' => $item,
                'i_product_type' => $product_type,
                's_source' => $source
                ));
            return $this->dao->insertedId();
        }

        public function insertPrice($category, $publish_fee, $premium_fee) {
            $this->dao->replace($this->getTable_prices(), array('fk_i_category_id' => $category, 'f_publish_cost' => $publish_fee, 'f_premium_cost' => $premium_fee));
        }

        public function payPublishFee($itemId, $paymentId) {
            $paid = $this->getPublishData($itemId);
            if(empty($paid)) {
                $this->createItem($itemId, 1, date("Y-m-d H:i:s"), $paymentId);
            } else {
                $this->dao->update($this->getTable_publish(), array('b_paid' => 1, 'dt_date' => date("Y-m-d H:i:s"), 'fk_i_payment_id' => $paymentId), array('fk_i_item_id' => $itemId));
            }
            $mItems = new ItemActions(false);
            $mItems->enable($itemId);
        }

        public function payPremiumFee($itemId, $paymentId) {
            $paid = $this->getPremiumData($itemId);
            if(empty($paid)) {
                $this->dao->insert($this->getTable_premium(), array('dt_date' => date("Y-m-d H:i:s"), 'fk_i_payment_id' => $paymentId, 'fk_i_item_id' => $itemId));
            } else {
                $this->dao->update($this->getTable_premium(), array('dt_date' => date("Y-m-d H:i:s"), 'fk_i_payment_id' => $paymentId), array('fk_i_item_id' => $itemId));
            }
            $mItem = new ItemActions(false);
            $mItem->premium($itemId, true);
        }

        public function addWallet($user, $amount) {
            $amount = (int)($amount*1000000000000);
            $wallet = $this->getWallet($user);
            if($wallet) {
                $this->dao->update($this->getTable_wallet(), array('i_amount' => $amount+$wallet['i_amount']), array('fk_i_user_id' => $user));
            } else {
                $this->dao->insert($this->getTable_wallet(), array('fk_i_user_id' => $user, 'i_amount' => $amount));
            }

        }

    }

?>