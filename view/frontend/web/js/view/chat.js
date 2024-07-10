define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'mage/translate',
    'domReady!',
], function ($, customerData, $t) {
    'use strict'

    function trackEvent(widget, eventName, eventData) {
        console.log('trackEvent', {
            eventName, eventData
        });
        widget.track(eventName, eventData);
    }

    function submitRestoreId(config, restoreId) {

        console.log('Submitting freshchat restoreId', restoreId);

        $.ajax({
            url: config['generic']['freshchatUpdateUrl'],
            type: 'POST',
            data: {
                'restore_id': restoreId
            },
            dataType: 'json',
            showLoader: false,
            success: function (json) {
                console.info('SUCCESS Submit freshchat restoreId');
            },
            error: function (a, b) {
                console.error('ERROR Submit freshchat restoreId', {
                    a, b
                });
            }
        });
    }

    function updateWidgetProperties(widget, customerData) {
        widget.user.setProperties({
            firstName: customerData.firstname || null,
            lastName: customerData.lastname || null,
            email: customerData.email || null,
            phone: customerData.phone || null,
            phoneCountryCode: customerData.phoneCountryCode || null,
        });

        console.info('freshchatContext.user', {
            user: widget.user
        });

        trackEvent(widget, 'testme', {
            xyz: 123
        });
    }

    function prepareLoadWidgetWithCustomerData(config, customerData) {

        window['fcWidgetMessengerConfig'] = {
            externalId: customerData.uid ? 'uuid-' + customerData.uid : null,
            restoreId: customerData.freshchatRestoreId || null
        };

        loadJsAsync('https://' + config['freshchat'].host + '/js/widget.js', function () {
            const widget = initFcWidget(config['freshchat']);

            if (widget) {
                widget.user.get(function (resp) {
                    var status = resp && resp.status,
                        data = resp && resp.data;
                    if (status !== 200) {
                        updateWidgetProperties(widget, customerData);
                        widget.on('user:created', function (resp) {
                            var status = resp && resp.status,
                                data = resp && resp.data;
                            if (status === 200) {
                                if (data.restoreId) {
                                    submitRestoreId(config, data.restoreId);
                                }
                            }
                        });
                    } else {
                        updateWidgetProperties(widget, customerData);
                    }
                });
            }
        });
    }

    function configureUserData(config) {
        const customerD = customerData.get('customer');
        const customer = customerD();

        if (!customer || !(customer.data_id)) {
            customerD.subscribe(function () {
                prepareLoadWidgetWithCustomerData(config, customerD());
            }, this);
        } else {
            prepareLoadWidgetWithCustomerData(config, customer);
        }
    }

    function loadJsAsync(scriptUri, completion) {
        var script = document.createElement("script");
        script.src = scriptUri;
        script.setAttribute('chat', 'true');
        script.onload = function () {
            completion();
        };
        document.head.appendChild(script);
    }

    function initFcWidget(config) {
        const fcWidget = window['fcWidget'] || null;

        if (!fcWidget) {
            console.warn('Could not load fcWidget');
            return;
        }

        console.log('initFcWidget', fcWidget);

        fcWidget.init({
            token: config.token,
            host: 'https://' + config.host,
            siteId: config.siteId,
        });

        return fcWidget;
    }

    function initialize(config) {
        if (config['freshchat'].enabled) {
            configureUserData(config);
        }
    }

    return function (config) {
        initialize(config);
    }
});
