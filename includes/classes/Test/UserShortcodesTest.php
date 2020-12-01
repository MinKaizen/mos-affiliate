<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\Mis;

use function \do_shortcode;
use function \update_user_meta;

class UserShortcodesTest extends Test {

  protected $_injected_user;


  protected function _before(): void {
    $this->_injected_user = $this->create_test_user();
    $this->set_user();
  }


  public function test_affid_shortcode(): void {
    $expected = $this->_injected_user->get_affid();
    $shortcode = '[mos_affid]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_email_shortcode(): void {
    $email = 'teEGRaghlR83SBEOfMCfYjNO4NIrHZvN@gmail.com';
    $this->_injected_user->user_email = $email;
    $shortcode = '[mos_email]';
    $this->assert_shortcode_equal( $shortcode, $email );
  }


  public function test_first_name(): void {
    $first_name = 'Hayasaka';
    $this->_injected_user->first_name = $first_name;
    $shortcode = '[mos_first_name]';
    $this->assert_shortcode_equal( $shortcode, $first_name );
  }


  public function test_last_name(): void {
    $last_name = 'Ai';
    $this->_injected_user->last_name = $last_name;
    $shortcode = '[mos_last_name]';
    $this->assert_shortcode_equal( $shortcode, $last_name );
  }
    

  public function test_level_shortcode(): void {
    $level_slug = 'monthly_partner';
    $level_name = 'Monthly Partner';
    $this->_injected_user->roles = [$level_slug];
    $shortcode = '[mos_level]';
    $this->assert_shortcode_equal( $shortcode, $level_name );
  }
    

  public function test_mis_shortcode(): void {
    $mis = [
      'gr' => 'my_gr_id',
      'cm' => '',
      'non_existent' => 'my_nonexistent_id',
    ];

    foreach( $mis as $slug => $value ) {
      $meta_key = Mis::MIS_META_KEY_PREFIX . $slug;
      update_user_meta( $this->_injected_user->ID, $meta_key, $value );
    }


    $expected = $mis['gr'];
    $shortcode = '[mos_mis network=gr]';
    $this->assert_shortcode_equal( $shortcode, $expected );

    $expected = $mis['cm'];
    $shortcode = '[mos_mis network=cm]';
    $this->assert_shortcode_equal( $shortcode, $expected );
    
    $expected = '';
    $shortcode = '[mos_mis network=non_existent]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_name_shortcode(): void {
    $first_name = 'Hayasaka';
    $last_name = 'Ai';
    $this->_injected_user->first_name = $first_name;
    $this->_injected_user->last_name = $last_name;

    $expected = "$first_name $last_name";
    $shortcode = '[mos_name]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_username_shortcode(): void {
    $expected = $this->_injected_user->user_login;
    $shortcode = '[mos_username]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_wpid_shortcode(): void {
    $expected = $this->_injected_user->ID;
    $shortcode = '[mos_wpid]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  private function assert_shortcode_equal( string $shortcode, $expected ): void {
    $output = do_shortcode( $shortcode );
    $this->assert_equal( $expected, $output, [
      'expected' => $expected,
      'shortcode' => $shortcode,
      'output' => $output,
    ] );
  }


}