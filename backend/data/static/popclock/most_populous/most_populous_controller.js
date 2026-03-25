function MostPopulousViewController(api, mostPopulousContainerID, $header)
{
    var $controller = this;
    this.api = api;
    this.mostPopulousContainerID = mostPopulousContainerID;
    this.$header = $header; // Expecting jQuery
    this.jQueryEquivalent = $('#' + this.mostPopulousContainerID);
    
    // Init
    this.prependString = '#populous-';
    this.table = null;
}

MostPopulousViewController.prototype = {
   createTables: function() {
      var controller    = this;
      var creatingTables = $.Deferred();
      var gettingJSON    = this.getJSON();
      gettingJSON.done(function( data ) {

      // primary uses config for tabs, titles, etc.
      // uses data to generate rows                
      var tables = [
         { 
            title: config.components.populous.tables[0].title,
            columns: [
               config.components.populous.tables[0].columns[0],
               config.components.populous.tables[0].columns[1] + ', ' + config.components.populous.tables[0].vintage,
               config.components.populous.tables[0].columns[2] + ', ' + config.components.populous.tables[0].vintage
            ],
            rows: controller.prepareRows(data.states),
            summary: config.components.populous.tables[0].table_summary
         },
         {
                        title: config.components.populous.tables[1].title,
                        columns: [
                            config.components.populous.tables[1].columns[0],
                            config.components.populous.tables[1].columns[1] + ', ' + config.components.populous.tables[1].vintage,
                            config.components.populous.tables[1].columns[2] + ', ' + config.components.populous.tables[1].vintage
                        ],
                        rows: controller.prepareRows(data.counties),
                        summary: config.components.populous.tables[1].table_summary
         },
         {
                        title: config.components.populous.tables[2].title,
                        columns: [
                            config.components.populous.tables[2].columns[0],
                            config.components.populous.tables[2].columns[1] + ', ' + config.components.populous.tables[2].vintage,
                            config.components.populous.tables[2].columns[2] + ', ' + config.components.populous.tables[2].vintage
                        ],
                        rows: controller.prepareRows(data.cities),
                        summary: config.components.populous.tables[2].table_summary
                        
         }
      ]; //end of var tables
    
      controller.table = new TabbedTable('#' + controller.mostPopulousContainerID, controller.prependString);
      controller.table.render(tables);
      controller.table.enable();
                
      creatingTables.resolve();
   });
            
      return creatingTables;
   },  //end of createTables()
   prepareRows: function ( data )
   {
      var rows = [];
      $.each(data, function ( index, row ) {
               
         rows[index] = [
            '<a href="' + row.quickfacts + '">' + row.name + '</a>',
            addCommas(row.population),
            addCommas(row.density)
         ];
      });
      return rows;
   },
   getJSON: function ()
   {
      var controller = this;
      return this.api.get('populous', {}, function( data, textStatus, jqXHR, promise) {
         promise.resolve(data);    

      });
   },
   getCSV: function()
   {
      var controller  = this;
      var creatingCSV = $.Deferred();
      var gettingJSON = this.getJSON();
      gettingJSON.done( function( data ) {
         var csv = [];
         // state
         csv.push(
            populous_density_csv_table(
               config.components.populous.tables[0].title,
               [
                  config.components.populous.tables[0].columns[0],
                  config.components.populous.tables[0].columns[1] + ', ' + config.components.populous.tables[0].vintage,
                  config.components.populous.tables[0].columns[2] + ', ' + config.components.populous.tables[0].vintage
               ],
               data.states
            )
         );
                
         // county
         csv.push(
            populous_density_csv_table(
               config.components.populous.tables[1].title,
               [
                  config.components.populous.tables[1].columns[0],
                  config.components.populous.tables[1].columns[1] + ', ' + config.components.populous.tables[1].vintage,
                  config.components.populous.tables[1].columns[2] + ', ' + config.components.populous.tables[1].vintage
               ],
               data.counties
            )
         );
                
         // city
         csv.push(
            populous_density_csv_table(
               config.components.populous.tables[2].title,
               [
                  config.components.populous.tables[2].columns[0],
                  config.components.populous.tables[2].columns[1] + ', ' + config.components.populous.tables[2].vintage,
                  config.components.populous.tables[2].columns[2] + ', ' + config.components.populous.tables[2].vintage
               ],
               data.cities
            )
         );
                
         creatingCSV.resolve(csv);
      });

      return creatingCSV;
   }
} //end of controller
