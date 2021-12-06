import {
    PerspectiveCamera,
    Scene,
    CylinderGeometry,
    TextureLoader,
    MeshBasicMaterial,
    Mesh,
    WebGLRenderer,
    MOUSE,
    TOUCH,
} from 'three';
import {OrbitControls} from 'three/examples/jsm/controls/OrbitControls.js'

export function carousel3d() {
    let camera, scene, renderer, cylinder, controls;

    let height = document.getElementById('carousel-3D').clientWidth;
    let width = document.getElementById('carousel-3D').clientWidth;

     init(window.textureUrl).then(() => {});

    async function init(url) {
        camera = new PerspectiveCamera(70, height / width, 0.01, 6000);
        camera.position.z = 1500;

        scene = new Scene();

        const geometry = new CylinderGeometry(898, 898, 416, 50);
        const loader = new TextureLoader();
        loader.load(url, (texture) => {
            const material = new MeshBasicMaterial({
                map: texture,
            });
            material.transparent = true;
            cylinder = new Mesh(geometry, material);
            scene.add(cylinder);

            renderer = new WebGLRenderer({antialias: true, alpha: true});
            renderer.setSize(height, width);
            renderer.setAnimationLoop(animation);
            document.getElementById('carousel-3D').appendChild(renderer.domElement);

            controls = new OrbitControls(camera, renderer.domElement);
            controls.autoRotate = true;
            controls.autoRotateSpeed = 1.7;
            controls.minPolarAngle = Math.PI / 2;
            controls.maxPolarAngle = Math.PI / 2;
            controls.mouseButtons = {
                LEFT  : MOUSE.ROTATE,
                MIDDLE: MOUSE.ROTATE,
                RIGHT : MOUSE.ROTATE
            };
            controls.touches = {
                ONE: TOUCH.ROTATE,
                TWO: TOUCH.ROTATE
            };
            controls.zoomSpeed = 0;
            controls.update();

            renderer.domElement.addEventListener('pointerdown', rotate, false);

            window.addEventListener('resize', onWindowResize, false);

            function onWindowResize() {
                height = document.getElementById('carousel-3D').clientWidth;
                width = document.getElementById('carousel-3D').clientWidth;

                camera.aspect = width / height;
                camera.updateProjectionMatrix();

                renderer.setSize(width, height);

            }
        });
    }

    function animation(time) {
        controls.update();
        renderer.render(scene, camera);
    }

    let timer = null;
    function rotate() {
        controls.autoRotate = false;

        if (timer) {
            clearTimeout(timer);
        }

        timer = setTimeout(function () {
            controls.autoRotate = true;
        }, 10000);
    }
}

