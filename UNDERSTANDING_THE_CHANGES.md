# Understanding the Changes: Legacy Labels to Modern Subsection Tabs

## What Changed in Your Menu/Tab Course Format?

The Menu/Tab course format has been updated to use a more modern and reliable approach for creating tabs within your course sections. This guide explains what changed and why it matters to you.

---

## The Old Way (Legacy Version)

### How It Used to Work

In the previous version, creating tabs required a special technique:

1. **Add a Label activity** to your section
2. **Edit the label** and switch to HTML editing mode
3. **Type special HTML code** like `<h2>Tab Name</h2>`
4. **Add your activities** after the label
5. When students viewed the section, the label with `<h2>` would become a tab

### Example of Old Method

```
Section 1: Week 1
├── 📄 Label with HTML: <h2>Introduction</h2>
├── 📹 Video: Welcome
├── 📖 Page: Syllabus
├── 📄 Label with HTML: <h2>Assessment</h2>
└── 📝 Quiz: Week 1 Quiz
```

When students viewed this section, they would see two tabs: "Introduction" and "Assessment"

### Problems with the Old Way

❌ **Required HTML knowledge** - Teachers needed to know how to write HTML code
❌ **Easy to make mistakes** - One wrong character could break the tabs
❌ **Hard to edit** - Changing tab names meant editing HTML
❌ **Not accessible** - Screen readers couldn't properly identify tabs
❌ **Labels cluttered editing view** - Teachers saw lots of label activities
❌ **Fragile** - Accidentally deleting or moving a label broke the tabs

---

## The New Way (Modern Version)

### How It Works Now

The new version uses Moodle's built-in **Subsection** feature:

1. **Click "Add subsection"** from the section menu
2. **Name the subsection** - this becomes your tab name
3. **Add activities to the subsection** - drag and drop like normal
4. When students view the section, subsections automatically appear as tabs

### Example of New Method

```
Section 1: Week 1
├── 📂 Subsection: Introduction
│   ├── 📹 Video: Welcome
│   └── 📖 Page: Syllabus
└── 📂 Subsection: Assessment
    └── 📝 Quiz: Week 1 Quiz
```

When students view this section, they see two tabs: "Introduction" and "Assessment"

### Benefits of the New Way

✅ **No HTML required** - Just name your subsection like any other activity
✅ **Easy to edit** - Change tab names by renaming the subsection
✅ **Better organization** - Clear structure in editing mode
✅ **Fully accessible** - Works perfectly with screen readers
✅ **Uses Moodle standards** - Built into Moodle 5, not a workaround
✅ **Future-proof** - Will continue to work in future Moodle versions
✅ **Harder to break** - Can't accidentally delete HTML and break tabs

---

## Side-by-Side Comparison

| Aspect | Old Way (Labels with HTML) | New Way (Subsections) |
|--------|---------------------------|----------------------|
| **How to create** | Add label → Edit HTML → Type `<h2>` | Click "Add subsection" → Name it |
| **Skill required** | Need to know HTML | Basic Moodle skills |
| **In editing mode** | See label activities with HTML | See subsection containers |
| **Editing tab names** | Edit HTML code | Rename subsection |
| **Moving activities** | Drag activities after correct label | Drag activities into subsection |
| **Accessibility** | Limited | Fully accessible |
| **Risk of breaking** | High (wrong HTML breaks it) | Low (standard Moodle) |
| **Learning curve** | Steep for non-technical users | Easy for all users |

---

## What Students See (No Difference!)

**Important:** From the student perspective, **nothing changes**! 

Students still see:
- Beautiful grid layout on the course homepage
- Tabs within each section when they click on a section card
- Content organized exactly as you intended

The improvement is entirely "behind the scenes" making it easier and more reliable for **you** as the course designer.

---

## What This Means for You

### If You're Creating a New Course

✨ **Great news!** You'll use the easier, modern method from the start.

**To create tabs in a section:**
1. Turn editing on
2. Go to the section where you want tabs
3. Click the three-dot menu (⋮) in the section
4. Select "Add subsection"
5. Name your subsection (this is your tab name)
6. Add activities to the subsection
7. Repeat for additional tabs
8. Turn editing off to see the tabs in action

### If You're Using an Existing Course

📋 **You have two options:**

#### Option 1: Keep Using Your Course As-Is
- If your course already has tabs created with the old method (labels with `<h2>`), **they still work**
- You don't have to change anything
- Your students won't notice any difference

#### Option 2: Convert to the Modern Method
- A **conversion tool** is available
- One-click conversion process
- Automatically converts all old-style tabs to modern subsections
- Recommended if you plan to make major updates to your course

### If You're Importing a Course

📦 **Automatic detection and easy conversion:**

When you import a course that uses the old method:
1. A **yellow warning banner** will appear on your course homepage
2. The banner says: "This course contains legacy labels with `<h2>` tags"
3. Click the **"Convert to modern tabs"** button
4. Confirm the conversion
5. Your course is automatically updated to use subsections

---

## Understanding the Conversion Process

### What Happens During Conversion?

When you convert a course from old to new:

**Before Conversion:**
```
Section: Week 1
├── 📄 Label: <h2>Introduction</h2>     ← Will be converted
├── 📹 Video: Welcome                   ← Will be moved
├── 📖 Page: Syllabus                   ← Will be moved
├── 📄 Label: <h2>Assessment</h2>       ← Will be converted
└── 📝 Quiz: Week 1 Quiz                ← Will be moved
```

**After Conversion:**
```
Section: Week 1
├── 📂 Subsection: Introduction         ← Created from label
│   ├── 📹 Video: Welcome               ← Moved here
│   └── 📖 Page: Syllabus               ← Moved here
└── 📂 Subsection: Assessment           ← Created from label
    └── 📝 Quiz: Week 1 Quiz            ← Moved here
```

### What Gets Changed?

✅ **Labels with `<h2>` tags** → Converted to subsections
✅ **Activities after labels** → Moved into corresponding subsections
✅ **Tab names** → Preserved exactly as they were
✅ **Activity order** → Maintained
✅ **Visibility settings** → Copied to subsections

### What Gets Deleted?

🗑️ **Old label activities** with `<h2>` tags are removed (no longer needed)

### What Stays the Same?

✓ All your actual course content (videos, quizzes, assignments, etc.)
✓ Section names and descriptions
✓ Course structure and organization
✓ Student submissions and grades
✓ All other labels (without `<h2>` tags)
✓ Activities that weren't part of tabs

---

## Visual Guide: Before and After

### Example: A Typical Course Week

**OLD WAY - Editing View:**
```
📚 Section 1: Week 1 - Introduction to Course
├── 📄 Label: "<h2>Getting Started</h2>"     ← Confusing HTML label
├── 📹 Video: Welcome to the Course
├── 📖 Page: Course Syllabus
├── 💬 Forum: Introduce Yourself
├── 📄 Label: "<h2>This Week's Learning</h2>" ← Another HTML label  
├── 📖 Page: Chapter 1 Reading
├── 📝 Assignment: Reflection Paper
├── 📄 Label: "<h2>Quiz</h2>"                ← Yet another HTML label
└── 📝 Quiz: Week 1 Knowledge Check
```

**NEW WAY - Editing View:**
```
📚 Section 1: Week 1 - Introduction to Course
├── 📂 Getting Started                        ← Clear subsection
│   ├── 📹 Video: Welcome to the Course
│   ├── 📖 Page: Course Syllabus
│   └── 💬 Forum: Introduce Yourself
├── 📂 This Week's Learning                   ← Clear subsection
│   ├── 📖 Page: Chapter 1 Reading
│   └── 📝 Assignment: Reflection Paper
└── 📂 Quiz                                   ← Clear subsection
    └── 📝 Quiz: Week 1 Knowledge Check
```

**STUDENT VIEW (Both Old and New):**
```
┌──────────────────┬────────────────────┬───────┐
│ Getting Started  │ This Week's Learning│ Quiz │  ← Tabs
└──────────────────┴────────────────────┴───────┘
[Content for selected tab displays here]
```

---

## Frequently Asked Questions

### Q: Do I have to convert my existing courses?
**A:** No, courses using the old method still work. However, converting is recommended for:
- Easier future maintenance
- Better accessibility
- Simpler editing experience
- Long-term course sustainability

### Q: Will converting break my course?
**A:** No, the conversion is safe and preserves all content. However:
- ✅ Always backup your course first (recommended)
- ✅ Test on a duplicate course if you're concerned
- ✅ Conversion is instantaneous and automatic
- ✅ Nothing is lost, only reorganized

### Q: Can students tell the difference?
**A:** No! Students see exactly the same tabs and content. The change only affects how teachers create and edit tabs.

### Q: What if I'm not comfortable with the conversion?
**A:** You have options:
1. Keep using the old method (it still works)
2. Ask your Moodle administrator for help
3. Convert a duplicate course first to see the results
4. Start using the new method only in new courses

### Q: Can I create new tabs the old way?
**A:** Technically yes, but we strongly recommend using subsections:
- Easier to create
- Easier to maintain
- Better for accessibility
- Future-proof

### Q: What happens to regular labels (without `<h2>`)?
**A:** They are **not affected** at all. Only labels containing `<h2>` tags are converted. Regular labels stay exactly as they are.

### Q: Will this affect my course completion tracking?
**A:** No! All completion tracking, grades, and student progress remain unchanged. Only the organization structure is updated.

### Q: Can I undo a conversion?
**A:** Not automatically, but you can restore from a backup. This is why we recommend backing up before converting.

### Q: Do I need special permissions?
**A:** You need teacher/editing rights in the course. Students and non-editing teachers won't see the conversion option.

---

## Tips for Success

### For Teachers New to Subsections

1. **Start small**: Try creating subsections in a test section first
2. **Watch it work**: Turn editing off to see how tabs appear
3. **Drag and drop**: Moving activities into subsections is just like moving them anywhere else
4. **Name clearly**: Subsection names become tab names, so keep them concise
5. **Limit tabs**: 3-5 tabs per section works best (too many gets cluttered)

### Best Practices with Subsections

✅ **Do:**
- Use clear, descriptive names (e.g., "Readings", "Activities", "Assessment")
- Keep tab names short (long names may wrap awkwardly)
- Group related content together
- Use consistent tab structure across sections

❌ **Avoid:**
- Too many tabs (more than 6 becomes overwhelming)
- Vague names (e.g., "Tab 1", "Content", "Stuff")
- Empty subsections (create the subsection, then add content immediately)
- Mixing old and new methods (convert fully or not at all)

---

## Getting Help

### Need Assistance with the Conversion?

1. **Your Moodle Administrator** - Can help with:
   - Running the conversion for you
   - Creating backups before conversion
   - Troubleshooting any issues

2. **Course Design Support** - Available for:
   - Learning how to use subsections
   - Reorganizing your course structure
   - Best practices for tab organization

3. **Technical Documentation** - Available in your course:
   - USER_GUIDE.md - Step-by-step instructions
   - LEGACY_CONVERSION_GUIDE.md - Detailed conversion process

### Training Resources

Ask your institution about:
- Workshops on the new course format features
- One-on-one support sessions
- Video tutorials
- Quick reference guides

---

## The Bottom Line

### What Changed?
The **method** for creating tabs changed from HTML labels to built-in subsections.

### What Stayed the Same?
The **appearance and functionality** for students is identical.

### Why Change?
To make course creation **easier**, **more reliable**, and **more accessible** for everyone.

### What Should You Do?

**If creating a new course:** Use the new subsection method from the start

**If maintaining an existing course:** Consider converting when you have time for a course refresh

**If importing a course:** Use the one-click conversion tool when prompted

**If unsure:** Keep using what you have; it still works perfectly

---

## Conclusion

The update from label-based tabs to subsection-based tabs represents a significant improvement in:

- **Ease of use** - No HTML knowledge required
- **Reliability** - Uses standard Moodle features
- **Accessibility** - Better for all students
- **Sustainability** - Future-proof approach
- **Professionalism** - Clean editing interface

While the change may seem technical, the practical impact is simple: **creating and managing tabs in your course is now easier and more reliable than ever before**.

Your courses will continue to look great, your students will have the same excellent experience, and you'll spend less time wrestling with HTML code and more time focusing on great content and pedagogy.

---

**Questions?** Contact your Moodle support team or instructional designer for personalized assistance.

**Ready to try it?** Check out the USER_GUIDE.md for step-by-step instructions on creating your first subsection-based tabs!

---

*Document Version: 1.0*  
*Last Updated: November 3, 2025*  
*For: Menu/Tab Course Format v3.0+*

