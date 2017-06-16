// ../app/config - is a trick to fool requirejs
require(['../app/config'], function () {
    require(['Nmotion'], function (NmotionApp) {
        NmotionApp.getInstance().run();
    });
});
