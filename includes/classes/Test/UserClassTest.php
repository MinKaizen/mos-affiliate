<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use MOS\Affiliate\Mis;

use function \MOS\Affiliate\ranstr;

class UserClassTest extends Test {


  public function test_construct(): void {
    $user = new User();
    $this->assert_instanceof( $user, 'MOS\Affiliate\User' );
  }


  public function test_construct_from_id(): void {
    $inserted_user = $this->create_test_user();
    $get_user = User::from_id( $inserted_user->ID );
    $this->assert_equal( $inserted_user, $get_user, [
      'User created via insert' => $inserted_user,
      'User created via User::from_id()' => $get_user,
    ] );
  }


  public function test_construct_from_affid(): void {
    $inserted_user = $this->create_test_user();
    $get_user = User::from_affid( $inserted_user->get_affid() );
    $this->assert_equal( $inserted_user, $get_user, [
      'User created via insert' => $inserted_user,
      'User created via User::from_affid()' => $get_user,
    ] );
  }


  public function test_construct_from_username(): void {
    $inserted_user = $this->create_test_user();
    $get_user = User::from_username( $inserted_user->user_login );
    $this->assert_equal( $inserted_user, $get_user, [
      'User created via insert' => $inserted_user,
      'User created via User::from_username()' => $get_user,
    ] );
  }


  public function test_id_exists(): void {
    $this->assert_true( User::id_exists(1), "#CRUDE TEST: assert that user with ID 1 exists." );
  }


  public function test_affid_exists(): void {
    $user = $this->create_test_user();
    $this->assert_true( User::affid_exists( $user->get_affid() ), "Affid of newly created user should exist" );
  }


  public function test_is_empty(): void {
    $user = new User();
    $this->assert_true( $user->is_empty() );
  }


  public function test_get_wpid(): void {
    $test_user_id = 42;
    $user = new User();
    $user->ID = $test_user_id;
    $this->assert_equal_strict( $user->get_wpid(), $test_user_id );
  }


  public function test_get_affid(): void {
    $user = $this->create_test_user();
    $this->assert_not_empty( $user->get_affid(), "UAP should automatically populare affid after insert. Check UAP settings" );
  }


  public function test_get_username(): void {
    $test_username = 'bfJd1shZEXTWpmFn2QSop6l8pGGkxfdR';
    $user = new User();
    $user->user_login = $test_username;
    $this->assert_equal_strict( $user->get_username(), $test_username );
  }


  public function test_get_name(): void {
    $first_name = 'Hayasaka';
    $last_name = 'Ai';
    $full_name = 'Hayasaka Ai';

    $user = new User();
    $user->first_name = $first_name;
    $user->last_name = $last_name;

    $this->assert_equal_strict( $user->get_first_name(), $first_name );
    $this->assert_equal_strict( $user->get_last_name(), $last_name );
    $this->assert_equal_strict( $user->get_name(), $full_name );
  }


  public function test_get_email(): void {
    $email = 'fuu.houhou@gmail.com';
    $user = new User();
    $user->user_email = $email;
    $this->assert_equal_strict( $user->get_email(), $email );
  }


  public function test_get_mis(): void {
    $user = $this->create_test_user();

    $id = $user->get_wpid();

    $mis_slug = 'key_not_in_config';
    $mis_meta_key = Mis::MIS_META_KEY_PREFIX . $mis_slug;
    $mis_value = 'some_value';
    $success = \update_user_meta( $id, $mis_meta_key , $mis_value );
    $this->assert_true( $success );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), '' );

    $mis_slug = 'gr';
    $mis_meta_key = Mis::MIS_META_KEY_PREFIX . $mis_slug;
    $mis_value = 'some_value';
    $success = \update_user_meta( $id, $mis_meta_key, $mis_value );
    $this->assert_true( $success );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), $mis_value );
  }


  public function test_get_level(): void {
    $user = new User();
    $user->roles = ['hello_world'];
    $this->assert_equal_strict( $user->get_level(), 'Hello World' );
    $user->roles = ['free'];
    $this->assert_equal_strict( $user->get_level(), 'Free Member' );
    $user->roles = ['monthly_partner'];
    $this->assert_equal_strict( $user->get_level(), 'Monthly Partner' );
    $user->roles = ['yearly_partner'];
    $this->assert_equal_strict( $user->get_level(), 'Yearly Partner' );
  }


  public function test_qualifies_for_mis(): void {
    $user = new User();
    
    $slug = 'gr';
    $mis = Mis::get( $slug );
    $this->assert_true_strict( $mis->exists() );
    $cap = $mis->get_cap();

    $this->assert_false_strict( $user->qualifies_for_mis( $slug ) );
    $user->add_cap( $cap );
    $this->assert_true_strict( $user->qualifies_for_mis( $slug ) );
  }


  public function test_db(): void {
    global $wpdb;
    
    $username = ranstr();
    $password = ranstr();
    $user = new User();
    $user->user_login = $username;
    $user->user_pass = $password;
    
    $sponsor_username = ranstr();
    $sponsor_password = ranstr();
    $sponsor = new User();
    $sponsor->user_login = $sponsor_username;
    $sponsor->user_pass = $sponsor_password;

    // insert user()
    $user->db_insert();
    $this->assert_not_empty( $wpdb->insert_id, 'wpdb->insert_id should not be empty after user->db_insert()' );
    $db_user = \get_user_by( 'login', $username );
    $this->assert_not_empty( $db_user, "Username $username should exist after db_insert()" );
    $this->assert_equal( $user->ID, $db_user->ID, "user->ID should equal db_user->ID after insert" );
    $this->assert_not_empty( $user->get_affid(), "UAP should auto register affid after db_insert(). Check UAP settings" );

    // add sponsor
    $sponsor->db_insert();
    $user->db_add_sponsor( $sponsor);
    $db_sponsor = $user->sponsor();
    $this->assert_equal( $sponsor->ID, $db_sponsor->ID, "Sponsor in DB should be same as sponsor in variable", $sponsor, $db_sponsor );

    // delete user
    $affid = $user->get_affid();
    $id = $user->ID;
    $user->db_delete();
    $this->assert_false( User::affid_exists( $affid ), "Affid $affid should be deleted after user->db_delete()" );
    $this->assert_false( User::id_exists( $id ), "User ID $id should not exist after user->db_delete" );
    $this->assert_true( $user->sponsor()->is_empty(), "Sponsor relationship should be deleted after user->db_delete()" );
    
    // delete sponsor
    $sponsor_affid = $sponsor->get_affid();
    $sponsor_id = $sponsor->ID;
    $sponsor->db_delete();
    $this->assert_false( User::affid_exists( $sponsor_affid ), "Affid $sponsor_affid should be deleted after sponsor->db_delete()" );
    $this->assert_false( User::id_exists( $sponsor_id ), "User ID $sponsor_id should not exist after sponsor->db_delete" );
  }


}