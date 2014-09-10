'use strict';

var todoContainer, username, password;

$(function() {
  todoContainer = $('#todo_container');
  username = prompt('Enter username:');
  password = prompt('Enter password:');
  loadTodoList();
});

function sendRequest(path, method, params, callback) {
  $.ajax({
    url: basepath + '/' + path,
    username: username,
    password: password,
    type: method,
    data: params,
    success: callback
  });
}

function loadTodoList() {
  sendRequest('todo', 'get', {}, function (res) {
    todoContainer.html('');
    for (var i in res) {
      todoContainer.append('<p>' + res[i].description +
        '<a href="#" onclick="updateTodo(' + res[i].id +
          '); return false">Update</a>\
      <a href="#" onclick="deleteTodo(' + res[i].id +
          '); return false">Delete</a></p>');
    }
  })
}

function updateTodo(id) {
  var desc = prompt('Enter new todo description:');
  sendRequest('todo/' + id, 'put', {desc: desc}, function (res) {
    loadTodoList();
  });
}

function newTodo() {
  var desc = prompt('Enter todo description:');
  sendRequest('todo', 'post', {desc: desc}, function (res) {
    loadTodoList();
  });
}

function deleteTodo(id) {
  if (!confirm('Are you sure you want to delete this todo?')) return;
  sendRequest('todo/' + id, 'delete', {}, function (res) {
    loadTodoList();
  });
}
