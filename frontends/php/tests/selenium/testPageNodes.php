<?php
/*
** Zabbix
** Copyright (C) 2000-2011 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/
?>
<?php
require_once dirname(__FILE__).'/../include/class.cwebtest.php';

class testPageNodes extends CWebTest {

	// Returns all nodes
	public static function allNodes() {

		return DBdata('SELECT * FROM nodes ORDER BY nodeid');
	}

	public function testPageNodes_StandaloneSetup() {

		$this->login('nodes.php');
		$this->checkTitle('Configuration of nodes');
		$this->ok('DM');
		$this->ok('CONFIGURATION OF NODES');
		if (0 == DBcount("select * from nodes order by nodeid")) {
			$this->ok('Your setup is not configured for distributed monitoring');
		}
	}

	/**
	* @dataProvider allNodes
	*/
	public function testPageNodes_CheckLayout($node) {

		$this->login('nodes.php');
		$this->checkTitle('Configuration of nodes');
		$this->ok(array('CONFIGURATION OF NODES', 'NODES', 'ID', 'Name', 'IP:Port'));
		$this->assertElementPresent('config');
		$this->assertElementPresent('form');
		$this->ok(array($node['name']));

	}

	/**
	* @dataProvider allNodes
	*/
	public function testPageNodes_SimpleUpdate($node) {

		$this->login('nodes.php');

		$sqlNodes = 'SELECT * FROM nodes ORDER BY nodeid';
		$oldHashNodes=DBhash($sqlNodes);

		$this->click('link='.$node['name']);
		$this->wait();
		$this->button_click('save');
		$this->wait();
		$this->ok('Node updated');

		$newHashNodes = DBhash($sqlNodes);
		$this->assertEquals($oldHashNodes, $newHashNodes, "Chuck Norris: no-change node update should not update data in table 'nodes'");

	}

}
?>
