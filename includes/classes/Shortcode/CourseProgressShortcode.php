<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class CourseProgressShortcode extends AbstractShortcode {

  protected $slug = 'mos_course_progress';

  public function shortcode_action( $args ): string {
    $progress = User::current()->get_free_course_progress()['formatted'];
    return $progress;
  }

}