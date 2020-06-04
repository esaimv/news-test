from django.core.management.base import BaseCommand, CommandError

CATEGORIES = (
    "Noticia",
    "Evento",
    "Curso",
)

class Command(BaseCommand):
    help = "Feed default data for models"

    def add_arguments(self, parser):
        parser.add_argument("model", type=str, help="Options are Publications Categories")

    def handle(self, *args, **options):
        feed_model = options["model"]

        if feed_model == "PublicationCategory":
            from publications.models import PublicationCategory

            for category in CATEGORIES:
                description = category
                category = PublicationCategory.objects.filter(description=description).first()
                if not category:
                    category = PublicationCategory(description=description)
                category.save()
            self.stdout.write(self.style.SUCCESS("Done"))
