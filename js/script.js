function checkAll() {
  var action = document.getElementById('check-all').checked;
  var boxes = document.getElementsByTagName('input');
  var len = boxes.length;
  
  for (var i = 0; i < len; i++) {
    if (boxes[i].type == 'checkbox') {
      boxes[i].checked = action;
    }
  }
}

function clearForm() {
  if (confirm('Clear form?')) {
    document.getElementById('name').value = '';
    document.getElementById('userfile').value = '';
    document.getElementById('err-name').innerHTML = '';
    document.getElementById('err-file').innerHTML = '';
  }
}

function deleteSelected() {
  var boxes = document.getElementsByTagName('input');
  var len = boxes.length;
  var res = false;
  
  for (var i = 0; i < len; i++) {
    if (boxes[i].type == 'checkbox' && boxes[i].checked) {
      res = true;
    }
  }
  
  if (res) {
    if (confirm('Delete selected items?')) {
      document.getElementById('file-form').submit();
    }
  } else {
    alert('Please check some files');
  }
}

function answerTo(left, right, level, post) {
  document.getElementById('answer').innerHTML = 'Answer to #' + post + ' comment';
  document.getElementById('left_key').value = left;
  document.getElementById('right_key').value = right;
  document.getElementById('level').value = level;
}

function cancelComment() {
  document.getElementById('answer').innerHTML = '';
  document.getElementById('left_key').value = document.getElementById('temp_left_key').value;
  document.getElementById('right_key').value = document.getElementById('temp_right_key').value;
  document.getElementById('level').value = document.getElementById('temp_level').value;
}