  var entypes = new Array();
  entypes.push("Task");
  entypes.push("Control");
  //entypes.push("Custom");

  function is_system_action(o) {
    //console.log(o);
    if(entypes.length>0) {
      for(var i=0;i<entypes.length;i++)  {
        //console.log(o);
        //console.log(entypes[i]);
        if(entypes[i]==o) return true;
      }
    }
    return false;
  }

  function do_empty_action_label() {
    $('label[for="LogEntryRelationID"]').html('<em>Select option above</em>');
    $('#LogEntryEntrytypeForeignKey').attr({"disabled":"disabled"});
  }

  function do_filled_action_label(v) {
    $('label[for="LogEntryRelationID"]').html(v + ' ID');
  }

  function handle_action_selection(v)  {
    if(is_system_action(v)) {
      do_relation_action(v);
    } else  {
      do_empty_action_label();
    }
  }

  function do_relation_action(v) {

    var data;
    if(is_first_load) {
      data = {id : $("#LogEntryEntrytypeForeignKey").val()};
      is_first_load = false;
    }

    if(v!='Custom') {
      if(v=='Task') {
        var url = "<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'get_list', 'plugin'=>false)); ?>";
      }
      if(v=='Control') {
        //alert('Jere');
        var url = "<?php echo $this->Html->url(array('controller'=>'controls', 'action'=>'get_list', 'plugin'=>false)); ?>";
      }
      //console.log(v);
      //console.log(url);
      $.ajax(url, {
        data: data,
        success:function(d) {
          //alert(d);
          $('#LogEntryEntrytypeForeignKey').parent('div.form-group').html(d);
        }
      })
    }
  }
  
  $('#LogEntryEntrytypeId').on('change', function()	{
    handle_action_selection($(this).val());
	})
  var is_first_load = true;
  handle_action_selection($('#LogEntryEntrytypeId').val(), true);