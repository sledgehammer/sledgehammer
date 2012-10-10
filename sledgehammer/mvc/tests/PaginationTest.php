<?php
namespace Sledgehammer;
/**
 * PaginationTests
 */
class PaginationTest extends TestCase {

	function test_pagination() {
		$pager = new Pagination(2, 1, array('href' => '#page'));
		$this->assertEquals(view_to_string($pager), '<div class="pagination"><ul>
	<li class="active"><a href="#page1">1</a></li>
	<li><a href="#page2">2</a></li>
	<li><a href="#page2">&raquo;</a></li>
</ul></div>', 'Pagination should not render a prev button');
	}
}

?>
