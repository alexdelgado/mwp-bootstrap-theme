<?php

class WP_Bootstrap_Walker_Nav_Menu extends Walker_Nav_Menu {
  static $instance = false;

  public $menu_item_parent = 0;

  public function __construct() {

    // remove Wordpress menu-item id
    add_filter( 'nav_menu_item_id', '__return_false', 100, 1 );

    // remove Wordpress menu classes
    add_filter( 'nav_menu_css_class', array( $this, 'nav_menu_css_class' ), 10, 4 );

    // add menu icon logic
    add_filter( 'nav_menu_item_title', array( $this, 'nav_menu_item_title' ), 10, 4 );

    // add screen reader text logic
    add_filter( 'nav_menu_link_attributes', array( $this, 'nav_menu_link_attributes' ), 10, 4 );
  }

  /**
   * Starts the list before the elements are added.
   *
   * @since 3.0.0
   *
   * @see Walker::start_lvl()
   *
   * @param string   $output Used to append additional content (passed by reference).
   * @param int      $depth  Depth of menu item. Used for padding.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   */
  public function start_lvl( &$output, $depth = 0, $args = null ) {

    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }

    $indent = str_repeat( $t, $depth );

    // Default class.
    $classes = array( 'sub-menu' );

    /**
     * Filters the CSS class(es) applied to a menu list element.
     *
     * @since 4.8.0
     *
     * @param string[] $classes Array of the CSS classes that are applied to the menu `<ul>` element.
     * @param stdClass $args    An object of `wp_nav_menu()` arguments.
     * @param int      $depth   Depth of menu item. Used for padding.
     */
    $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

    // Add sub-menu ID and aria-labelledby attributes
    $attrs = sprintf( 'id="nav-list-%1$d" aria-labelledby="nav-link-%1$d"', $args->walker->menu_item_parent );

    $output .= "{$n}{$indent}<ul$class_names $attrs>{$n}";
  }

  /**
   * Starts the element output.
   *
   * @since 3.0.0
   * @since 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
   *
   * @see Walker::start_el()
   *
   * @param string   $output Used to append additional content (passed by reference).
   * @param WP_Post  $item   Menu item data object.
   * @param int      $depth  Depth of menu item. Used for padding.
   * @param stdClass $args   An object of wp_nav_menu() arguments.
   * @param int      $id     Current item ID.
   */
  public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {

    if ( empty( $item->menu_item_parent ) ) {
      $this->menu_item_parent = $item->ID;
    }

    if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
      $t = '';
      $n = '';
    } else {
      $t = "\t";
      $n = "\n";
    }

    $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

    $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
    $classes[] = 'menu-item-' . $item->ID;

    /**
     * Filters the arguments for a single nav menu item.
     *
     * @since 4.4.0
     *
     * @param stdClass $args  An object of wp_nav_menu() arguments.
     * @param WP_Post  $item  Menu item data object.
     * @param int      $depth Depth of menu item. Used for padding.
     */
    $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

    /**
     * Filters the CSS classes applied to a menu item's list item element.
     *
     * @since 3.0.0
     * @since 4.1.0 The `$depth` parameter was added.
     *
     * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
     * @param WP_Post  $item    The current menu item.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
     * @param int      $depth   Depth of menu item. Used for padding.
     */
    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

    /**
     * Filters the ID applied to a menu item's list item element.
     *
     * @since 3.0.1
     * @since 4.1.0 The `$depth` parameter was added.
     *
     * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
     * @param WP_Post  $item    The current menu item.
     * @param stdClass $args    An object of wp_nav_menu() arguments.
     * @param int      $depth   Depth of menu item. Used for padding.
     */
    $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
    $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

    $output .= $indent . '<li' . $id . $class_names . '>';

    $atts           = array();
    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target'] = ! empty( $item->target ) ? $item->target : '';

    if ( '_blank' === $item->target && empty( $item->xfn ) ) {
      $atts['rel'] = 'noopener noreferrer';
    } else {
      $atts['rel'] = $item->xfn;
    }

    $atts['href']         = ! empty( $item->url ) ? $item->url : '';
    $atts['aria-current'] = $item->current ? 'page' : '';

    /**
     * Filters the HTML attributes applied to a menu item's anchor element.
     *
     * @since 3.6.0
     * @since 4.1.0 The `$depth` parameter was added.
     *
     * @param array $atts {
     *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
     *
     *     @type string $title        Title attribute.
     *     @type string $target       Target attribute.
     *     @type string $rel          The rel attribute.
     *     @type string $href         The href attribute.
     *     @type string $aria_current The aria-current attribute.
     * }
     * @param WP_Post  $item  The current menu item.
     * @param stdClass $args  An object of wp_nav_menu() arguments.
     * @param int      $depth Depth of menu item. Used for padding.
     */
    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

    $attributes = '';

    foreach ( $atts as $attr => $value ) {
      if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
        $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
        $attributes .= ' ' . $attr . '="' . $value . '"';
      }
    }

    /** This filter is documented in wp-includes/post-template.php */
    $title = apply_filters( 'the_title', $item->title, $item->ID );

    /**
     * Filters a menu item's title.
     *
     * @since 4.4.0
     *
     * @param string   $title The menu item's title.
     * @param WP_Post  $item  The current menu item.
     * @param stdClass $args  An object of wp_nav_menu() arguments.
     * @param int      $depth Depth of menu item. Used for padding.
     */
    $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

    $item_output  = $args->before;
    $item_output .= '<a' . $attributes . '>';
    $item_output .= $args->link_before . $title . $args->link_after;
    $item_output .= '</a>';
    $item_output .= $args->after;

    /**
     * Filters a menu item's starting output.
     *
     * The menu item's starting output only includes `$args->before`, the opening `<a>`,
     * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
     * no filter for modifying the opening and closing `<li>` for a menu item.
     *
     * @since 3.0.0
     *
     * @param string   $item_output The menu item's starting HTML output.
     * @param WP_Post  $item        Menu item data object.
     * @param int      $depth       Depth of menu item. Used for padding.
     * @param stdClass $args        An object of wp_nav_menu() arguments.
     */
    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

  /**
   * Filters the CSS classes applied to a menu item's list item element.
   *
   * @since 3.0.0
   * @since 4.1.0 The `$depth` parameter was added.
   *
   * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
   * @param WP_Post  $item    The current menu item.
   * @param stdClass $args    An object of wp_nav_menu() arguments.
   * @param int      $depth   Depth of menu item. Used for padding.
   */
  public function nav_menu_css_class( $classes, $item, $args, $depth ) {

    if ( ! empty( $classes ) ) {

      foreach ( $classes as $i => $class ) {
        if ( preg_match( '/btn|icon-|js-|menu-|screen-reader-text/', $class, $matches ) ) {
          unset($classes[$i]);
        }
      }
    }

    return $classes;
  }

  /**
   * Filters a menu item's title.
   *
   * @since 4.4.0
   *
   * @param string   $title The menu item's title.
   * @param WP_Post  $item  The current menu item.
   * @param stdClass $args  An object of wp_nav_menu() arguments.
   * @param int      $depth Depth of menu item. Used for padding.
   */
  public function nav_menu_item_title( $title, $item, $args, $depth ) {
    $classes = '';

    foreach ( $item->classes as $class ) {
      if ( $this->_has_icon_class( $class ) ) {
        $classes .= '<i class="'. $class .'"></i> ';
      }

      if ( $this->_has_screen_reader_class( $class ) ) {
        $title = '<span class="'. $class .'">'. $title .'</span>';
      }
    }

    return $title . $classes ;
  }

  /**
   * Filters the HTML attributes applied to a menu item's anchor element.
   *
   * @since 3.6.0
   * @since 4.1.0 The `$depth` parameter was added.
   *
   * @param array $atts {
   *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
   *
   *     @type string $title        Title attribute.
   *     @type string $target       Target attribute.
   *     @type string $rel          The rel attribute.
   *     @type string $href         The href attribute.
   *     @type string $aria_current The aria-current attribute.
   * }
   * @param WP_Post  $item  The current menu item.
   * @param stdClass $args  An object of wp_nav_menu() arguments.
   * @param int      $depth Depth of menu item. Used for padding.
   */
  public function nav_menu_link_attributes( $atts, $item, $args, $depth ) {

    if ( empty( $atts['class'] ) ) {
      $atts['class'] = '';
    }

    foreach( $item->classes as $class ) {

      if ( $this->_has_button_class( $class ) ) {
        $atts['class'] .= " $class";
      }

      if ( $this->_has_javascript_class( $class ) ) {
        $atts['class'] .= " $class";
      }

      if ( $this->_has_children_class( $class ) ) {
        $atts['id'] = 'nav-link-'. $this->menu_item_parent;
        $atts['role'] = 'button';
        $atts['aria-controls'] = 'nav-list-'. $this->menu_item_parent;
        $atts['aria-expanded'] = 'false';
        $atts['aria-haspopup'] = 'true';
      }
    }

    return $atts;
  }

  /**
   * Singleton
   *
   * Returns a single instance of this class.
   */
  public static function singleton() {

    if ( ! self::$instance ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Determines whether the given input contains any button classes.
   *
   * @param string $class CSS class applied to a menu item's `<li>` element.
   */
  protected function _has_button_class( $class ) {
    return preg_match( '/btn/', $class );
  }

  /**
   * Determines whether the given input contains a child menu class.
   *
   * @param string $class CSS class applied to a menu item's `<li>` element.
   */
  protected function _has_children_class( $class ) {
    return preg_match( '/menu-item-has-children/', $class );
  }

  /**
   * Determines whether the given input contains any icon classes.
   *
   * @param string $class CSS class applied to a menu item's `<li>` element.
   */
  protected function _has_icon_class( $class ) {
    return preg_match( '/icon-/', $class );
  }

  /**
   * Determines whether the given input contains any javascript classes.
   *
   * @param string $class CSS class applied to a menu item's `<li>` element.
   */
  protected function _has_javascript_class( $class ) {
    return preg_match( '/js-/', $class );
  }

  /**
   * Determines whether the given input contains a screen-reader class.
   *
   * @param string $class CSS class applied to a menu item's `<li>` element.
   */
  protected function _has_screen_reader_class( $class ) {
    return preg_match( '/screen-reader-text/', $class );
  }
}
