if ( ! Detector.webgl ) Detector.addGetWebGLMessage();

var container, stats;

var camera, cameraTarget, scene, renderer;

var firstModel = '../../models/stanford_bunny.ply';
var secondModel = '../../models/stanford_bunny.ply,';

init(firstModel,firstModel);
animate();

function init(firstModel,secondModel) {

    container = document.createElement( 'div' );
    document.getElementById("screen").appendChild( container );

    camera = new THREE.PerspectiveCamera( 10, window.innerWidth / window.innerHeight, 1, 15 );
    camera.position.set( 3, 0.15, 3 );

    cameraTarget = new THREE.Vector3( 0, -0.1, 0 );

    scene = new THREE.Scene();
    scene.fog = new THREE.Fog( 0x037371, 2, 15 );


    // Ground

    var plane = new THREE.Mesh(
        new THREE.PlaneBufferGeometry( 40, 40 ),
        new THREE.MeshPhongMaterial( { color: 0x05b56c, specular: 0x101010 } )
    );
    plane.rotation.x = -Math.PI/2;
    plane.position.y = -0.8;
    scene.add( plane );

    plane.receiveShadow = true;


    // PLY file

    var loader = new THREE.PLYLoader();
    loader.load( 'res/models/'+firstModel, function ( geometry ) {

        geometry.computeVertexNormals();

        var material = new THREE.MeshStandardMaterial( { color: 0xFDD835, shading: THREE.FlatShading } );
        var mesh = new THREE.Mesh( geometry, material );

        mesh.position.y = -0.1;
        mesh.position.z = 0.1;
        mesh.rotation.x = 0;

        mesh.castShadow = true;
        mesh.receiveShadow = true;

        scene.add( mesh );

    } );
    loader.load( 'res/models/'+secondModel, function ( geometry ) {

        geometry.computeVertexNormals();

        var material = new THREE.MeshStandardMaterial( { color: 0xE65100, shading: THREE.FlatShading } );
        var mesh = new THREE.Mesh( geometry, material );

        mesh.position.y = -0.3;
        mesh.position.z = 0.1;
        mesh.rotation.x = 0;

        mesh.castShadow = true;
        mesh.receiveShadow = true;

        scene.add( mesh );

    } );
    // Lights

    scene.add( new THREE.HemisphereLight( 0x443333, 0x111122 ) );

    addShadowedLight( 1, 1, 1, 0xffffff, 1.35 );
    addShadowedLight( 0.5, 1, -1, 0xffaa00, 1 );

    // renderer

    renderer = new THREE.WebGLRenderer( { antialias: true } );
    renderer.setClearColor( scene.fog.color );
    renderer.setPixelRatio( window.devicePixelRatio );
    renderer.setSize( window.innerWidth, window.innerHeight );

    renderer.gammaInput = true;
    renderer.gammaOutput = true;

    renderer.shadowMap.enabled = true;
    renderer.shadowMap.renderReverseSided = false;

    container.appendChild( renderer.domElement );

    // stats

    stats = new Stats();
    container.appendChild( stats.dom );

    // resize

    window.addEventListener( 'resize', onWindowResize, false );

}

function addShadowedLight( x, y, z, color, intensity ) {

    var directionalLight = new THREE.DirectionalLight( color, intensity );
    directionalLight.position.set( x, y, z );
    scene.add( directionalLight );

    directionalLight.castShadow = true;

    var d = 1;
    directionalLight.shadow.camera.left = -d;
    directionalLight.shadow.camera.right = d;
    directionalLight.shadow.camera.top = d;
    directionalLight.shadow.camera.bottom = -d;

    directionalLight.shadow.camera.near = 1;
    directionalLight.shadow.camera.far = 4;

    directionalLight.shadow.mapSize.width = 1024;
    directionalLight.shadow.mapSize.height = 1024;

    directionalLight.shadow.bias = -0.005;

}

function onWindowResize() {

    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();

    renderer.setSize( window.innerWidth, window.innerHeight );

}

function animate() {

    requestAnimationFrame( animate );

    render();
    stats.update();

}

function render() {

    var timer = Date.now() * 0.0005;

    camera.position.x = Math.sin( timer ) * 2.5;
    camera.position.z = Math.cos( timer ) * 2.5;

    camera.lookAt( cameraTarget );

    renderer.render( scene, camera );

}
function loadPLY(){
    var file = document.getElementById("file-loader").innerHTML;
    console.log(file);
}
function readSingleFile(evt) {
    //Retrieve the first (and only!) File from the FileList object
    var f = evt.target.files[0];
    init (f,null);
}