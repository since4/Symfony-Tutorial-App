/*Main component: invoked from show.html.twig ReactDom.render 
 * with parameter: url
 * this.states: compontent internal variables
 * this.props: component external variables passed from components higher up 
 * If you want the data in your app to change — for example based on user interactions — 
 * it must be stored in a component’s state somewhere in the app.
 * */
var NoteSection = React.createClass({
    
    /*To initialize the state simply pass a getInitialState() to the component, 
     * and return whatever state you want your component to be initialized with.
     * Here an empty states varialble: notes*/
    getInitialState: function() {
        return {
            notes: []
        }
    },
	
    /*update after 2000 ms*/
    componentDidMount: function() {
        this.loadNotesFromServer();
        setInterval(this.loadNotesFromServer, 30000);
    },
	
    /*load json data from ajax API*/
    loadNotesFromServer: function() {
        /*$.ajax: gets url and function as parameters
            * it calls url
            * fetches json data structure
            * feeds json data into function success 
            * where the variable notes get the data 
            * via function setState*/
           $.ajax({
            /*To modify the state, simply call this.setState(), 
                    passing in the new state as the argument.
                    Here: load json data structure with notes accessable at url
                    get stored into state variable:notes*/
            //url: '/genus/octopus/notes',
            url: this.props.url,
            success: function (data) {
                this.setState({notes: data.notes});
            }.bind(this)
        });
    },
	
    /**/
    render: function() {
        return (
            <div>
                <div className="notes-container">
                    <h2 className="notes-header">Notes</h2>
                    <div><i className="fa fa-plus plus-btn"></i></div>
                </div>
                    <NoteList notes={this.state.notes} />
            </div>
        );
    }
});

/*Nested component: imported into the return statement of the render function
	of variable NoteSection*/
var NoteList = React.createClass({
    render: function() {       
        /*maps each note in notes and returns a NoteBox
          which is stored in noteNodes and inserted below*/
        var noteNodes = this.props.notes.map(function(note) {
            return (
                <NoteBox username={note.username} 
                    avatarUri={note.avatarUri} 
                    date={note.date} 
                    key={note.id}>{note.note}
                </NoteBox>
            );
        });

        /*get input noteNodes from above*/
        return (
            <section id="cd-timeline">
                {noteNodes}
            </section>
        );
    }
});

/*Nested component: imported into the return statement of the render function
	of variable NoteList, and there into the return statement
	of the variable noteNodes*/
var NoteBox = React.createClass({
    render: function() {
        return (
            <div className="cd-timeline-block">
                <div className="cd-timeline-img">
                    <img src={this.props.avatarUri} className="img-circle" alt="Leanna!" />
                </div>
                <div className="cd-timeline-content">
                    <h2><a href="#">{this.props.username}</a></h2>
                    <p>{this.props.children}</p>
                    <span className="cd-date">{this.props.date}</span>
                </div>
            </div>
        );
    }
});

window.NoteSection = NoteSection;
