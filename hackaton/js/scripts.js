/**
 * Created by Piszi on 2017.1.8..
 */

if(!($("#map").length==0)) {
    $.getScript("https://maps.googleapis.com/maps/api/js?key=AIzaSyBgnjCUdrCEbkidyaQRu_1KvBndKSKxnEE&callback=offset&v=3&libraries=geometry");
    var map, current_pos, last_pos, LatLng = {lat: 46.099523, lng: 19.69981}, jstring, id, arrCoords = [], route,
        dataJSON = {
            coord: {lat: 0, lng: 0},
            altitude: 0,
            accuracy: 0,
            run_date: ""
        }, datas = [],oldlat, c_distance = 0, sum_distance = 0, last_time = 0, time, time_dif,update,flag=false;

    function offset() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: LatLng,
            zoom: 16
        });}
    $(document).ready(function () {

        if (navigator.geolocation) {
            var opt = {
                enableHighAccuracy: true,
                timeout: (5000),
                maximumAge: 0
            };

            id = navigator.geolocation.watchPosition(success, fail, opt);

            function success(pos) {

                LatLng = {lat: parseFloat(pos.coords.latitude), lng: parseFloat(pos.coords.longitude)};
                {
                    dataJSON.altitude = pos.coords.altitude;
                    dataJSON.accuracy = pos.coords.accuracy;
                    dataJSON.coord = LatLng;

                    time = new Date();
                    dataJSON.run_date = time.toISOString().slice(0, 10) + " " + time.toLocaleTimeString();
                }

                map.setCenter(LatLng);

                if (current_pos == null || last_pos != LatLng) {
                    if (current_pos != null)   current_pos.setMap(null);
                    arrCoords.push(LatLng);
                    current_pos = new google.maps.Marker({
                        position: LatLng,
                        map: map,
                        title: "Jelenlegi pozicio",
                        label: 'B'
                    });
                    if(last_pos==null)
                        last_pos=current_pos;
                    if (last_time != 0) {
                        time_dif = time - last_time;
                        if(time_dif<5) return;
                    }

                    jstring = JSON.stringify(dataJSON);
                    datas.push(jstring);

                    localStorage.setItem("data", datas);
                    {
                        route = null;
                        route = new google.maps.Polyline({
                            path: arrCoords,
                            strokeColor: "#FF0000",
                            strokeOpacity: 1.0,
                            strokeWeight: 4,
                            map: map
                        });
                    }


                    if (last_pos != null) {
                        c_distance = getDistance(last_pos.position, LatLng);
                         sum_distance += c_distance;
                        //$("#distance").text(Number(sum_distance).toFixed(1));
                        //$("#speed").text(Number((3600 * c_distance) / (time_dif)).toFixed(1));
                    }

                    last_pos = current_pos;

                    if(!flag)
                    {update=setInterval(function (){
                            jstring = JSON.stringify(datas);
                           // console.log(jstring);
                            $.post("tracker.php?record=record", {data: jstring}, function (data) {
                                if (data.length != 0){
                                    var l = JSON.parse(data);
                                    $("#speed").text(l.speed);
                                    $("#distance").text(l.sum);
                                  }

                            }).done(function()
                            {
                                datas = [];
                                localStorage.clear();

                            })
                                .fail(function () {
                                    console.warn("Feltöltés nem sikerült");
                                });},30000); flag=true;}
                    }

                    last_time = time;
                    setTimeout(function () {
                    }, 5000);
                }
                function fail(err) {
                console.log(err);
                setTimeout(function () {
                }, 5000);

            }
        }
        else document.write("<h1>A böngészője nem támogatja a pozicíó megadását.</h1>");
    });

    var rad = function (x) {
        return x * Math.PI / 180;
    };

    function getDistance(p1, p2) {
        var R = 6378137; // Earth’s mean radius in meter
        var dLat = rad(p2.lat - p1.lat);
        var dLong = rad(p2.lng - p1.lng);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.cos(rad(p1.lat)) * Math.cos(rad(p2.lat)) *
            Math.sin(dLong / 2) * Math.sin(dLong / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        var d = R * c;
        return d; // returns the distance in meter
    }


    }
    $("#end").click(function () {

            jstring = JSON.stringify(datas);
            console.log(jstring);
            $.post("tracker.php?record=record", {data: jstring}, function (data) {
                if (data.length != 0){
                    var l = JSON.parse(data);
                    $("#speed").text(l.speed);
                    $("#distance").text(l.sum);
                }

            })
           .done(function()
            {
                datas = [];
                localStorage.clear();
				window.location.href="tracker.php?mod=end";

            })
           .fail(function () {
                    console.warn("Feltöltés nem sikerült");
           });
    });









