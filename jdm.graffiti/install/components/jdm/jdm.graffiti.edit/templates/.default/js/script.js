(function($, config) {
    $(function() {

        var Strategy = function() {};

        Strategy.prototype.init = function($el) {
            this.$el = $el;
        };

        Strategy.prototype.save = function() {
            throw new Error('Strategy #execute needs to be overridden.')
        };


        var NewGraffitiStrategy = function() {};

        NewGraffitiStrategy.prototype = Object.create(Strategy.prototype);

        NewGraffitiStrategy.prototype.save = function(image) {
            var password = this.$el.passwordField.val();

            if (!password.trim()) {
                alert('Пароль не задан!');
                return;
            }

            $.ajax({
                'type': 'POST',
                'url': config.urlApiSave,
                'dataType': 'json',
                'data': {
                    'new': true
                    ,'image': image
                    ,'password': password
                },
                'success': function (response) {
                    if (!response.error) {
                        window.location.href = response.url;
                        return;
                    }

                    alert(response.error);
                }
            });
        };


        var AvailableGraffitiStrategy = function() {};

        AvailableGraffitiStrategy.prototype = Object.create(Strategy.prototype);

        AvailableGraffitiStrategy.prototype.init = function($el) {
            this.$el = $el;

            var password = prompt('Введи пароль для редактирования');

            this.$el.saveButton.prop('disabled', true);
            this.$el.canvas.css('pointer-events', 'none');

            if (!password) {
                alert('Редактирование запрещено');

                return;
            }

            var self = this;

            $.ajax({
                'type': 'POST',
                'url': config.urlApiCheck,
                'dataType': 'json',
                'data': {
                    'id': this.$el.canvas.data('id')
                    ,'password': password
                },
                'success': function (response) {
                    if (!response.error) {
                        self.token = response.token;
                        self.$el.saveButton.prop('disabled', false);
                        self.$el.canvas.css('pointer-events', '');
                        return;
                    }

                    alert(response.error);
                }
            });
        };

        AvailableGraffitiStrategy.prototype.save = function(image) {
            $.ajax({
                'type': 'POST',
                'url': config.urlApiSave,
                'dataType': 'json',
                'data': {
                    'id': this.$el.canvas.data('id')
                    ,'image': image
                    ,'token': this.token
                },
                'success': function (response) {
                    if (!response.error) {
                        alert('Сохранено успешно!');
                        return;
                    }

                    alert(response.error);
                }
            });
        };


        var Graffiti = function($GraffitiBlock, strategy) {
            this.strategy = strategy;
            this.config = config;

            this.$el = {
                'canvas': $GraffitiBlock.find('.j-jdm_graffiti-canvas')
                ,'canvasResult': $GraffitiBlock.find('.j-jdm_graffiti-canvas_result')
                ,'saveButton': $GraffitiBlock.find('.j-jdm_graffiti-save')
                ,'passwordField': $GraffitiBlock.find('.j-jdm_graffiti-password')
            };

            this.init();
        };

        Graffiti.prototype.canvasInit = function() {
            var imagePath = this.$el.canvas.data('path');

            this.image = new Image();

            if (imagePath) {
                this.image.src = imagePath;
            }

            this.$el.canvas.sketch();
        };

        Graffiti.prototype.prepareCanvasToSave = function() {
            var canvas = this.$el.canvasResult[0];
            var context = canvas.getContext('2d');

            context.fillStyle = 'white';
            context.fillRect(0, 0, canvas.width, canvas.height);

            context.drawImage(this.image, 0, 0);

            context.drawImage(this.$el.canvas[0],0,0)
        };

        Graffiti.prototype.init = function() {
            var self = this;

            this.canvasInit();
            this.strategy.init(this.$el);

            this.$el.saveButton.on('click', function() {
                self.prepareCanvasToSave();

                var image = self.$el.canvasResult[0].toDataURL('image/png');
                self.strategy.save(image);
            });
        };


        var $graffitiBlock = $('.j-jdm_graffiti');
        var graffitiStrategy;

        if (config.isNew) {
            graffitiStrategy = new NewGraffitiStrategy();
        } else {
            graffitiStrategy = new AvailableGraffitiStrategy();
        }

        $graffiti = new Graffiti($graffitiBlock, graffitiStrategy);
    });
})(jQuery, jdm_graffiti)
