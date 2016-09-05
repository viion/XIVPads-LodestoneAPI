//
// Hold achievement data
//
class AppAchievementViewClass
{
    constructor()
    {
        this.data = {};
        this.character = null;
    }

    //
    // Set achievement data and character
    //
    setData(data, character)
    {
        this.data = data;
        this.character = character;
    }
}

// Export it
module.exports = new AppAchievementViewClass;
