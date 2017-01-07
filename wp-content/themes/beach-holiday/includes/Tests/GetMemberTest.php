<?php
/**
 * ${user}
 * ${date}
 * ${project_name}
 **/

namespace Tests;

class MemberTest extends \WP_UnitTestCase {
	public function setUp() {
		$this->Member = new Member();
	}

	public function tearDown() {
		unset( $this->Member );
	}

	public function testMemberData() {
		$input  = '1691';
		$output = $this->Member->getdata( $input );
		$this->assertMemberID( 1691, $output, 'MemberID should be 1691 for this test' );
	}

}