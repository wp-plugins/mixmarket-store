<?php
class MM_Breadcrumbs {
	private $links = array();
	private $home = '';

	public function  __construct( $home = '' ) {
		$this->set_home( $home );
	}

	public function add_link( $link ) {
		$this->links[] = $link;
	}

	public function set_home( $link ) {
		$this->home = $link;
	}

	public function render( $delimeter = '&nbsp;&raquo;&nbsp;' ) {
		$html = $this->home;
		foreach ( $this->links as $link ) {
			$html .= $delimeter . $link;
		}
		return $html;
	}
}