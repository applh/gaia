<html>

<head>
    <script src="https://aframe.io/releases/1.3.0/aframe.min.js"></script>
    <script src="https://unpkg.com/aframe-environment-component/dist/aframe-environment-component.min.js"></script>
    <script src="https://unpkg.com/aframe-extras@6.1.1/dist/aframe-extras.min.js"></script>
    
</head>

<body>
    <a-scene>
        <a-assets>
            <a-asset-item id="bottle" src="/media/glb/bottle.glb"></a-asset-item>
        </a-assets>
        <a-entity gltf-model="#bottle" position="0 1 -2"></a-entity>
        <a-entity gltf-model="url(/media/glb/monsters.glb)" scale="1 1 1" animation-mixer="" position="0 0.1 -6"></a-entity>
        
        <a-box position="-1 0.5 -3" rotation="0 45 0" color="#4CC3D9"></a-box>
        <a-sphere position="0 1.25 -5" radius="1.25" color="#EF2D5E"></a-sphere>
        <a-cylinder position="1 0.75 -3" radius="0.5" height="1.5" color="#FFC65D"></a-cylinder>
        <a-plane position="0 0.1 -4" rotation="-90 0 0" width="4" height="4" color="#7BC8A4"></a-plane>
        <a-sky color="#ECECEC"></a-sky>

        <a-entity environment="preset: forest; dressingAmount: 500"></a-entity>

    </a-scene>
</body>

</html>