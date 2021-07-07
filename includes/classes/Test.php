<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \Exception;
use \WP_CLI;
use function \WP_CLI\Utils\format_items;

class Test {

  public function run(): void {
    $this->_set_up();
    $this->_before();
    foreach( $this->get_test_methods() as $method ) {
      $this->run_method( $method );
    }
    $this->_after();
    $this->_clean_up();
  }


  protected function _before(): void {
    // To be overridden for each individual test
  }
  
  
  protected function _after(): void {
    // To be overridden for each individual test
  }


  // Common setup for all tests
  protected final function _set_up(): void {
    
  }
  
  
  // Common cleanup for all tests
  protected final function _clean_up(): void {

  }


  private function get_test_methods(): array {
    $methods = get_class_methods( get_class( $this ) );
    $test_methods = [];

    foreach( $methods as $method ) {
      if ( strpos( $method, 'test_'  ) === 0 ) {
        $test_methods[] = $method;
      }
    }

    return $test_methods;
  }


  private function run_method( string $method ): void {
    $this->notice( "$method starting..." );
    $this->{$method}();
    $this->notice_success( "$method finished" );
  }


  private function get_class_name( bool $verbose=false ): string {
    $class_name = get_class( $this );

    if ( $verbose === false ) {
      // Get class name stub
      foreach ( explode( '\\', $class_name ) as $part ) {
        $class_name_short = $part;
      }
      $class_name = $class_name_short;
    }

    return $class_name;
  }


  protected function assert_is_int( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = is_int( $var );
    $data['var'] = $var;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_arrays_equal( $array1, $array2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = (
      is_array( $array1 )
      && is_array( $array2 )
      && count( $array1 ) == count( $array2 )
      && array_diff( $array1, $array2 ) === array_diff( $array2, $array1 )
    );
    $data['array1'] = $array1;
    $data['array2'] = $array2;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_arrays_not_equal( $array1, $array2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = !(
      is_array( $array1 )
      && is_array( $array2 )
      && count( $array1 ) == count( $array2 )
      && array_diff( $array1, $array2 ) === array_diff( $array2, $array1 )
    );
    $data['array1'] = $array1;
    $data['array2'] = $array2;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_is_number( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = is_int( $var ) || is_float( $var );
    $data['var'] = $var;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_greater_than( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 > $var2;
    $data['comparison'] = "$var1 > $var2";
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_greater_than_or_equal( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 >= $var2;
    $data['comparison'] = "$var1 >= $var2";
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_less_than( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 < $var2;
    $data['comparison'] = "$var1 < $var2";
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_less_than_or_equal( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 <= $var2;
    $data['comparison'] = "$var1 <= $var2";
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_is_array( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = is_array( $var );
    $data['var'] = $var;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_is_string( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = is_string( $var );
    $data['var'] = $var;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_is_url( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = filter_var( $var, FILTER_VALIDATE_URL );
    $data['var'] = $var;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_equal( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 == $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_equal_strict( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 === $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_not_equal( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 != $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_not_equal_strict( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 !== $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_true( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == true;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_true_strict( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === true;
    $data['expression'] = $expression;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_false( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == false;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_false_strict( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === false;
    $data['expression'] = $expression;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_empty( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = empty( $var );
    $data['var'] = $var;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_not_empty( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = ! empty( $var );
    $data['var'] = $var;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_string_contains( string $haystack, string $needle, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = ( strpos( $haystack, $needle ) !== false );
    $data['haystack'] = $haystack;
    $data['needle'] = $needle;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_string_not_contains( string $haystack, string $needle, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = ( strpos( $haystack, $needle ) === false );
    $data['haystack'] = $haystack;
    $data['needle'] = $needle;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_array_has_key( array $array, $key, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = array_key_exists( $key, $array );
    $data['array'] = $array;
    $data['key'] = $key;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_array_contains( array $array, $item, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = in_array( $item, $array );
    $data['array'] = $array;
    $data['item'] = $item;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_instanceof( $instance, string $class, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $instance instanceof $class;
    $data['instance'] = $instance;
    $data['class'] = $class;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_class_exists( string $class_name, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = class_exists( $class_name );
    $data['class_name'] = $class_name;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_redirect( string $entry_path, string $redirected_path, ...$data ): void {
    $assertion = __FUNCTION__;
    $this->db_notice( 'HTTP GET: ' . $entry_path );
    $res = wp_remote_get( home_url( $entry_path ) )['http_response']->get_response_object() ?? null;
    $this->assert_not_empty( $res, ['msg' => 'error during wp_remote_get() in Test class', 'url' => $entry_path] );
    $actual_url = \trim( $res->url, '/' );
    $expected_url = \trim( \home_url( $redirected_path ), '/' );
    $condition = $actual_url == $expected_url;
    $data['entry_path'] = $entry_path;
    $data['expected_path'] = $redirected_path;
    $data['expected_url'] = $expected_url;
    $data['actual_url'] = $actual_url;
    $this->assert( $condition, $assertion, $data );
  }


  protected function assert_db_row_exists( string $table, Object $row_data, bool $only_one=true ): void {
    #TODO
  }


  protected function assert( $condition, string $assertion, $data=[] ): void {
    if ( $condition ) {
      return;
    }

    $e = new Exception();
    $trace_string = $e->getTraceAsString();
    $trace_formatted = $this->format_trace( $trace_string );
    
    WP_CLI::line( $trace_formatted );

    $this->print_yaml( $data );
    $this->_after();
    $this->_clean_up();
    $this->print_error( $assertion );
  }

  
  protected function print_error( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%1%W' . $message . '%n%N');
    WP_CLI::error( $colorized_message );
  }
  
  
  protected function db_notice( string $message ): void {
    $class_name = $this->get_class_name();
    $colorized_message = WP_CLI::colorize( "%c♦%n $class_name: $message" );
    WP_CLI::debug( $colorized_message, 'mosa test' );
  }


  protected function notice( string $message ): void {
    $class_name = $this->get_class_name();
    $colorized_message = WP_CLI::colorize( "%k●%n $class_name: $message" );
    WP_CLI::debug( $colorized_message, 'mosa test' );
  }


  protected function notice_success( string $message ): void {
    $class_name = $this->get_class_name();
    $colorized_message = WP_CLI::colorize( "%g✔ $class_name: $message%n" );
    WP_CLI::debug( $colorized_message, 'mosa test' );
  }


  protected function print_yaml( $original ): void {
    if ( empty( $original ) ) {
      WP_CLI::warning( "No debug data passed" );
      return;
    }
    $debug_dump = [
      [
        'debug_dump' => $original,
      ],
    ];

    format_items( 'yaml', $debug_dump, 'debug_dump' );
  }


  protected function format_trace( string $original ): string {
    $exploded = explode("\n", print_r( $original, true));

    // Cut and colorize
    foreach ( $exploded as &$line ) {
      $line = preg_replace( '/(.*)(\/[a-zA-Z0-9]+\.php)(\(\d+\))(.*)/', '%5%W$2%6%W$3%n%N$4', $line );
      $line = WP_CLI::colorize( $line );
    }

    $formatted = 'Stack trace:' . PHP_EOL;

    foreach ( $exploded as $line ) {
      if ( strpos( $line, "Test.php" ) !== false ) {
        $formatted .= $line . PHP_EOL;
      } else {
        break;
      }
    }

    return $formatted;
  }


}