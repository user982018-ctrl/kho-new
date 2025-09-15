<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        table {
            border-collapse: collapse;
        }
        td, th {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background: linear-gradient(to right, tomato, white, tomato);
            cursor: move;
        }
    </style>
</head>
<body>
    hi
    <table id="myTable">
        <thead>
            <tr>
                <th draggable="true" >
                    Column -1
                </th>
                <th draggable="true" >
                    Column -2
                </th>
                <th draggable="true">
                    Column -3
                </th>
                <th draggable="true">
                    Column -4
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
            </tr>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
            </tr>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
            </tr>
            <tr>
                <td>1</td>
                <td>2</td>
                <td>3</td>
                <td>4</td>
            </tr>
        </tbody>
    </table>
    
<script type="text/javascript">
    var dragCol = null;
    function handleDragStart(e) {
        console.log('handleDragStart')
        dragCol = this;
        e.dataTransfer.efferAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
        console.log(e.dataTransfer)
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        return false;
    }

    function handleDrop(e) {
        console.log('drop');
        if (e.stopPropagation) {
            e.stopPropagation;
        }

        if (dragCol !== this) {
            var sourceIndex = Array.from(dragCol.parentNode.children).indexOf(dragCol);
            var targetIndex = Array.from(this.parentNode.children).indexOf(this);

            var table = document.getElementById('myTable');
            var rows = table.rows;
            console.log(rows);
            for (var i = 0; i < rows.length; i++) {
                
                var sourceCell = rows[i].cells[sourceIndex];
                var targetCell = rows[i].cells[targetIndex];
                
                var tempHTML = sourceCell.innerHTML;
                sourceCell.innerHTML = targetCell.innerHTML;
                targetCell.innerHTML = tempHTML;
            }
        }
        return false;
    }

    var cols = document.querySelectorAll('th');
    [].forEach.call(cols, function(col) {
        col.addEventListener('dragstart', handleDragStart, false);
        col.addEventListener('dragover', handleDragOver, false);
        col.addEventListener('drop', handleDrop, false);
    });

</script>
</body>
</html>