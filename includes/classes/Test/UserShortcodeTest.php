<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use MOS\Affiliate\Database;

class UserShortcodeTest extends Test {

  public function test_main(): void {
    $this->assert_true( 1==1 );
  }


  private function set_user( User $user ): void {
    \add_filter( 'mos_current_user', function() use ($user) {
      return $user;
    } );
  }

  
  private function create_user( string $username ): User {
    // Create User
    $id = \wp_insert_user([
      'user_login' => $username,
    ]);
    
    // Register user as affiliate
    $db = new Database();
    $success = $db->register_affiliate( $id );

    $this->assert_is_int( $id, $id );
    $this->assert_true_strict( $success );
    $this->db_notice( "$id - user created" );

    $user = User::from_id( $id );
    return $user;
  }


  private function delete_user( int $id ): void {
    // Delete User
    \wp_delete_user( $id );
    $user_exists = (\get_user_by( 'id', $id ) !== false);
    
    // Remove affiliate ID
    global $wpdb;
    $table = $wpdb->prefix . 'uap_affiliates';
    $columns = ['uid' => $id];
    $formats = ['uid' => '%d'];
    $rows_deleted = $wpdb->delete( $table, $columns, $formats );
    
    $this->assert_false_strict( $user_exists );
    $this->assert_not_equal_strict( $rows_deleted, false );
    $this->db_notice( "$id - user deleted" );
  }


}