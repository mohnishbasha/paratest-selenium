<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Todos.php';

class TodoTest extends PHPUnit_Extensions_Selenium2TestCase
{
    private $todos;

    public function setUp()
    {
        PHPUnit_Extensions_Selenium2TestCase::shareSession(true);
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://backbonejs.org/examples/todos/');
        $this->todos = new Todos($this->prepareSession());
    }

    public function testTypingIntoFieldAndHittingEnterAddsTodo()
    {
        $this->todos->addTodo("parallelize phpunit tests\n");
        $this->assertEquals(1, sizeof($this->todos->getItems()));
    }

    public function testClickingTodoCheckboxMarksTodoDone()
    {
        $this->todos->addTodo("make sure you can complete todos");
        $item = array_shift($this->todos->getItems());
        $this->todos->getItemCheckbox($item)->click();
        $this->assertEquals('done', $item->attribute('class'));
    }

    public function testCheckingAllTodosMarksToggleAllAsChecked()
    {
        $this->todos->addTodos(array("one", "two"));
        foreach($this->todos->getItems() as $item)
            $this->todos->getItemCheckbox($item)->click();
        $this->assertEquals('true', $this->todos->getToggleAll()->attribute('checked'));
    }

    public function testCheckingAllTodosAndUncheckingOneWillUncheckToggleAll()
    {
        $this->todos->addTodos(array("one", "two"));
        foreach($this->todos->getItems() as $item)
            $this->todos->getItemCheckbox($item)->click();
        $this->todos->getItemCheckbox(array_shift($this->todos->getItems()))->click();
        $this->assertNull($this->todos->getToggleAll()->attribute('checked'));
    }

    public function testClickingToggleAllWillMarkAllTodosAsComplete()
    {
        $this->todos->addTodos(array("three", "four"));
        $this->todos->getToggleAll()->click();
        foreach($this->todos->getItems() as $item)
            $this->assertEquals('done', $item->attribute('class'));
    }

    public function testClickingToggleAllAgainWillMarkAllTodosAsInComplete()
    {
        $this->todos->addTodos(array("three", "four"));
        $this->todos->getToggleAll()->click();
        $this->todos->getToggleAll()->click();
        foreach($this->todos->getItems() as $item)
            $this->assertNull($this->todos->getItemCheckbox($item)->attribute('checked'));
    }

    public function testAddingOneItemSetsCountToOneAndHasSingularTerm()
    {
        $this->todos->addTodo("make sure terms updated");
        $this->assertEquals("1 item left", $this->todos->getTodoCount()->text());
    }

    public function testAddingTwoItemsSetsCountToTwoAndHasPluralTerm()
    {
        $this->todos->addTodos(array('one', 'two'));
        $this->assertEquals("2 items left", $this->todos->getTodoCount()->text());
    }

    public function testCheckingItemsAsDoneSetsCountToZeroAndHasPluralTerm()
    {
        $this->todos->addTodo("make sure terms updated");
        $todo = array_shift($this->todos->getItems());
        $this->todos->getItemCheckbox($todo)->click();
        $this->assertEquals('0 items left', $this->todos->getTodoCount()->text());
    }

    public function testDestroyWillRemoveItem()
    {
        $this->todos->addTodo("make sure it can be destroyed");
        $todo = array_shift($this->todos->getItems());
        $this->todos->getItemDestroy(0, $todo)->click();
        $this->assertEquals(0, sizeof($this->todos->getItems()));
    }

}