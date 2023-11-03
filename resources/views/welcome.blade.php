<!DOCTYPE html>
<html>
<head>
    <style>
        #container {
            width: 300px;
            height: 300px;
            border: 1px solid #ccc;
            position: relative;
        }

        .draggable {
            width: 100px;
            height: 30px;
            background-color: #3498db;
            color: #fff;
            text-align: center;
            line-height: 30px;
            cursor: move;
            position: absolute;
        }
    </style>
</head>
<body>
    <div id="container">
        <div class="draggable" draggable="true">Văn bản 1</div>
        <div class="draggable" draggable="true">Văn bản 2</div>
        <div class="draggable" draggable="true">Văn bản 3</div>
    </div>

    <script>
        const draggables = document.querySelectorAll('.draggable');
        let currentDraggable = null;

        draggables.forEach(draggable => {
            draggable.addEventListener('dragstart', () => {
                currentDraggable = draggable;
                setTimeout(() => {
                    draggable.style.display = 'none';
                }, 0);
            });

            draggable.addEventListener('dragend', () => {
                setTimeout(() => {
                    draggable.style.display = 'block';
                    currentDraggable = null;
                }, 0);
            });
        });

        const container = document.getElementById('container');

        container.addEventListener('dragover', (e) => {
            e.preventDefault();
        });

        container.addEventListener('drop', (e) => {
            if (currentDraggable) {
                e.preventDefault();
                const x = e.clientX - container.getBoundingClientRect().left;
                const y = e.clientY - container.getBoundingClientRect().top;
                currentDraggable.style.left = x + 'px';
                currentDraggable.style.top = y + 'px';
            }
        });
    </script>
</body>
</html>
