@extends('app')

@section('content')
<div id="taskgrid" class="mt-4"><div>
@endsection
@section('pagejs')
<script>
var projectsds = ['API integrations','Billing system','Marketing emails'];
taskds = {!! json_encode($tasks) !!};
var taskgrid = new ej.grids.Grid({
        dataSource: taskds,
        allowFiltering: true,
        allowRowDragAndDrop: true,
        editSettings: { allowEditing: true, allowAdding: true, allowDeleting: true, mode: 'Normal', newRowPosition:'Top' },
        filterSettings: { type:'CheckBox' },
        toolbar: ['Add', 'Edit', 'Delete', 'Update', 'Cancel'],
        columns: [
            {
                field: 'id', isPrimaryKey: true, headerText: 'ID', textAlign: 'Right', visible: false,
            },
            {
                field: 'task', headerText: 'Task',allowFiltering: false,
                validationRules: { required: true },
            },
            {
                field: 'project', headerText: 'Project', editType: 'dropdownedit', dataSource: projectsds,
                edit: { params: { popupHeight: '300px', query: new ej.data.Query(), dataSource: projectsds } },
                validationRules: { required: true },
            },
            {
                field: 'priority', headerText: 'Priority', allowEditing: false, width:150,allowFiltering: false,
            }
        ],
        actionComplete: actionComplete,
        rowDrop: rowDrop,
},'#taskgrid');

function actionComplete(args){
  // add new record
  if(args.requestType == 'save' && args.action == 'add'){

    $.ajax({
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader ("Accept", "application/json");
        },
        type: "POST",
        url: "tasks",
        data: args.data,
        success: function (result) {

          refresh();
        },
    });
  }
  if(args.requestType == 'save' && args.action == 'edit'){

    $.ajax({
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader ("Accept", "application/json");
        },
        type: "PUT",
        url: "tasks/"+args.data.id,
        data: args.data,
        success: function (result) {
          refresh();
        },
    });
  }
  if(args.requestType == 'delete'){
    $.ajax({
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader ("Accept", "application/json");
        },
        type: "DELETE",
        url: "tasks/"+args.data[0].id,
        success: function (result) {
          refresh();
        },
    });
  }
}

function rowDrop(args){

  var sort_data = JSON.stringify({"id" : args.data[0].id, "from_index" : args.fromIndex, "drop_index": args.dropIndex});
  $.ajax({
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      beforeSend: function (xhr) {
        xhr.setRequestHeader ("Accept", "application/json");
      },
      type: "POST",
      url: "sort",
      datatype: "json",
      contentType: "application/json; charset=utf-8",
      data: sort_data,
      success: function (result) {
        refresh();
      },
  });
}

function refresh(){

    $.ajax({
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function (xhr) {
          xhr.setRequestHeader ("Accept", "application/json");
        },
        type: "POST",
        url: "datasource",
        datatype: "json",
        contentType: "application/json; charset=utf-8",
        success: function (result) {
          taskgrid.dataSource = result;
          taskgrid.dataBind();
        },
    });
}
</script>
@endsection
